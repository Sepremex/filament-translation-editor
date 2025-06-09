<?php
/*
 * @package     :   Sepremex ManagerFile
 * @handler     :   Master Router
 * @developer   :   Ruben Mc
 * @contact     :   webcorm@gmail.com
 * @company     :   Sepremex | TranslationSyncService.php
 * @date        :   6/9/2025 | 11:12
*/

namespace Sepremex\FilamentTranslationEditor\Services;

use Sepremex\FilamentTranslationEditor\Services\LanguageFileReader;
use Sepremex\FilamentTranslationEditor\Services\LanguageFileWriter;
use Sepremex\FilamentTranslationEditor\Services\LanguageManager;
use Sepremex\FilamentTranslationEditor\Services\Translation\TranslationManager;

class TranslationSyncService
{
    protected LanguageFileReader $reader;
    protected LanguageFileWriter $writer;
    protected LanguageManager $languageManager;
    protected TranslationManager $translationManager;

    public function __construct(
        LanguageFileReader $reader,
        LanguageFileWriter $writer,
        LanguageManager $languageManager,
        TranslationManager $translationManager
    ) {
        $this->reader = $reader;
        $this->writer = $writer;
        $this->languageManager = $languageManager;
        $this->translationManager = $translationManager;
    }

    /**
     * Sync a key to other available languages for regular files
     */
    public function syncKeyToOtherLanguages(string $currentLang, string $fileName, string $key, string $value, string $operation = 'add'): array
    {
        $syncedLanguages = [];
        $availableLanguages = $this->getAvailableLanguages();

        // Prepare for auto-translation if adding
        $autoTranslations = [];
        if ($operation === 'add' && $this->translationManager->isAutoTranslationEnabled()) {
            $targetLanguages = array_diff($availableLanguages, [$currentLang]);

            if (!empty($targetLanguages)) {
                try {
                    $result = $this->translationManager->translateToMultiple($value, $currentLang, $targetLanguages);
                    $autoTranslations = $result['translations'];

                    // Log any translation errors
                    if (!empty($result['errors'])) {
                        foreach ($result['errors'] as $lang => $error) {
                            \Log::warning("Auto-translation failed for {$lang}: {$error}");
                        }
                    }
                } catch (\Exception $e) {
                    \Log::warning("Auto-translation service failed: " . $e->getMessage());
                }
            }
        }

        foreach ($availableLanguages as $lang) {
            // Skip current language
            if ($lang === $currentLang) {
                continue;
            }

            try {
                $existingTranslations = $this->reader->read($lang, $fileName);

                if ($operation === 'add') {
                    // Only add if key doesn't exist
                    if (!array_key_exists($key, $existingTranslations)) {
                        // Use auto-translated value if available, otherwise use original
                        $translatedValue = $autoTranslations[$lang] ?? $value;
                        $existingTranslations[$key] = $translatedValue;

                        if ($this->writer->write($lang, $fileName, $existingTranslations)) {
                            $syncedLanguages[] = $lang;
                        }
                    }
                } elseif ($operation === 'remove') {
                    // Remove key if it exists
                    if (array_key_exists($key, $existingTranslations)) {
                        unset($existingTranslations[$key]);

                        if ($this->writer->write($lang, $fileName, $existingTranslations)) {
                            $syncedLanguages[] = $lang;
                        }
                    }
                }
            } catch (\Exception $e) {
                // Skip languages that don't have this file or have errors
                continue;
            }
        }

        return $syncedLanguages;
    }

    /**
     * Sync a key to other available languages for vendor files
     */
    public function syncKeyToOtherVendorLanguages(string $package, string $currentLang, string $fileName, string $key, string $value, string $operation = 'add'): array
    {
        $syncedLanguages = [];
        $availableLanguages = $this->getAvailableVendorLanguages($package);

        // Prepare for auto-translation if adding
        $autoTranslations = [];
        if ($operation === 'add' && $this->translationManager->isAutoTranslationEnabled()) {
            $targetLanguages = array_diff($availableLanguages, [$currentLang]);

            if (!empty($targetLanguages)) {
                try {
                    $result = $this->translationManager->translateToMultiple($value, $currentLang, $targetLanguages);
                    $autoTranslations = $result['translations'];

                    // Log any translation errors
                    if (!empty($result['errors'])) {
                        foreach ($result['errors'] as $lang => $error) {
                            \Log::warning("Vendor auto-translation failed for {$lang}: {$error}");
                        }
                    }
                } catch (\Exception $e) {
                    \Log::warning("Vendor auto-translation service failed: " . $e->getMessage());
                }
            }
        }

        foreach ($availableLanguages as $lang) {
            // Skip current language
            if ($lang === $currentLang) {
                continue;
            }

            try {
                $existingTranslations = $this->languageManager->readVendorTranslationFile($package, $lang, $fileName);

                if ($operation === 'add') {
                    // Only add if key doesn't exist
                    if (!$this->arrayKeyExistsFlat($key, $existingTranslations)) {
                        // Use auto-translated value if available, otherwise use original
                        $translatedValue = $autoTranslations[$lang] ?? $value;
                        $this->setArrayValueByKey($existingTranslations, $key, $translatedValue);

                        if ($this->languageManager->writeVendorTranslationFile($package, $lang, $fileName, $existingTranslations)) {
                            $syncedLanguages[] = $lang;
                        }
                    }
                } elseif ($operation === 'remove') {
                    // Remove key if it exists
                    if ($this->arrayKeyExistsFlat($key, $existingTranslations)) {
                        $this->unsetArrayValueByKey($existingTranslations, $key);

                        if ($this->languageManager->writeVendorTranslationFile($package, $lang, $fileName, $existingTranslations)) {
                            $syncedLanguages[] = $lang;
                        }
                    }
                }
            } catch (\Exception $e) {
                // Skip languages that don't have this file or have errors
                continue;
            }
        }

        return $syncedLanguages;
    }

    /**
     * Get available languages for regular files
     */
    protected function getAvailableLanguages(): array
    {
        $languagePath = config('filament-translation-editor.language_path');
        $languages = [];

        if (!is_dir($languagePath)) {
            return $languages;
        }

        $directories = scandir($languagePath);

        foreach ($directories as $dir) {
            if ($dir === '.' || $dir === '..' || $dir === 'vendor') {
                continue;
            }

            $fullPath = $languagePath . DIRECTORY_SEPARATOR . $dir;

            // Check if it's a directory (language folder) or JSON file
            if (is_dir($fullPath)) {
                $languages[] = $dir;
            } elseif (pathinfo($dir, PATHINFO_EXTENSION) === 'json') {
                $languages[] = pathinfo($dir, PATHINFO_FILENAME);
            }
        }

        return array_unique($languages);
    }

    /**
     * Get available languages for vendor package
     */
    protected function getAvailableVendorLanguages(string $package): array
    {
        $vendorPath = config('filament-translation-editor.language_path') . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . $package;
        $languages = [];

        if (!is_dir($vendorPath)) {
            return $languages;
        }

        $directories = scandir($vendorPath);

        foreach ($directories as $dir) {
            if ($dir === '.' || $dir === '..') {
                continue;
            }

            $fullPath = $vendorPath . DIRECTORY_SEPARATOR . $dir;

            if (is_dir($fullPath)) {
                $languages[] = $dir;
            }
        }

        return $languages;
    }

    /**
     * Check if a flattened key exists in nested array
     */
    protected function arrayKeyExistsFlat(string $key, array $array): bool
    {
        $flatArray = \Sepremex\FilamentTranslationEditor\Utils\ArrayHelper::flatten($array);
        return array_key_exists($key, $flatArray);
    }

    /**
     * Set value in nested array using <~> notation key
     */
    protected function setArrayValueByKey(array &$array, string $key, $value): void
    {
        // Only use <~> as separator for nested arrays
        if (!str_contains($key, '<~>')) {
            // No separator found, treat as literal key
            $array[$key] = $value;
            return;
        }

        $keys = explode('<~>', $key);
        $current = &$array;

        foreach ($keys as $k) {
            if (!isset($current[$k]) || !is_array($current[$k])) {
                $current[$k] = [];
            }
            $current = &$current[$k];
        }

        $current = $value;
    }

    /**
     * Unset value in nested array using <~> notation key
     * Also removes empty parent arrays recursively
     */
    protected function unsetArrayValueByKey(array &$array, string $key): void
    {
        // Only use <~> as separator for nested arrays
        if (!str_contains($key, '<~>')) {
            // No separator found, treat as literal key
            if (isset($array[$key])) {
                unset($array[$key]);
            }
            return;
        }

        $keys = explode('<~>', $key);

        // Call recursive function to unset and clean empty parents
        $this->unsetKeyRecursive($array, $keys, 0);
    }

    /**
     * Recursively unset key and clean empty parent arrays
     */
    private function unsetKeyRecursive(array &$array, array $keys, int $depth): bool
    {
        $currentKey = $keys[$depth];

        // If we're at the final key, unset it
        if ($depth === count($keys) - 1) {
            if (isset($array[$currentKey])) {
                unset($array[$currentKey]);
                return empty($array); // Return true if array is now empty
            }
            return false;
        }

        // If current key doesn't exist or isn't array, nothing to do
        if (!isset($array[$currentKey]) || !is_array($array[$currentKey])) {
            return false;
        }

        // Recursively process next level
        $shouldRemoveChild = $this->unsetKeyRecursive($array[$currentKey], $keys, $depth + 1);

        // If child array is empty after removal, remove it too
        if ($shouldRemoveChild) {
            unset($array[$currentKey]);
            return empty($array); // Return true if this array is now empty too
        }

        return false;
    }
}
