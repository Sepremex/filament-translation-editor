<?php
/*
 * @package     :   Sepremex ManagerFile
 * @handler     :   Master Router
 * @developer   :   Ruben Mc
 * @contact     :   webcorm@gmail.com
 * @company     :   Sepremex | LanguageManager.php
 * @date        :   6/4/2025 | 21:59
*/

namespace Sepremex\FilamentTranslationEditor\Services;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Sepremex\FilamentTranslationEditor\Utils\ArrayHelper;

class LanguageManager
{


    public function __construct(
        protected Filesystem $files
    ) {}

    public function getAvailableLanguages(): array
    {
        $path = config('filament-translation-editor.language_path', base_path('lang'));

        if (! $this->files->isDirectory($path)) {
            return [];
        }

        $directories = collect($this->files->directories($path))
            ->map(fn ($dir) => basename($dir))
            ->filter(fn ($lang) => preg_match('/^[a-z]{2}(-[A-Z]{2})?$/', $lang))
            ->values()
            ->all();

        $jsonFiles = [];
        if (config('filament-translation-editor.support_json')) {
            $jsonFiles = collect($this->files->files($path))
                ->filter(fn ($file) => str_ends_with($file->getFilename(), '.json'))
                ->map(fn ($file) => basename($file->getFilename(), '.json'))
                ->values()
                ->all();
        }

        return collect(array_merge($directories, $jsonFiles))
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    public function getPhpFiles(string $language): array
    {
        $path = base_path(config('filament-translation-editor.path', 'lang')) . DIRECTORY_SEPARATOR . $language;

        if (! $this->files->isDirectory($path)) {
            return [];
        }

        return collect($this->files->files($path))
            ->filter(fn ($file) => str_ends_with($file->getFilename(), '.php'))
            ->map(fn ($file) => $file->getFilename())
            ->sort()
            ->values()
            ->all();
    }

    public function hasJsonFile(string $language): bool
    {
        if (! config('filament-translation-editor.support_json')) {
            return false;
        }

        $path = base_path(config('filament-translation-editor.path', 'lang')) . DIRECTORY_SEPARATOR . $language . '.json';

        return $this->files->exists($path);
    }

    /**
     * Get vendor packages
     */
    public function getVendorPackages(): Collection
    {
        if (!config('filament-translation-editor.read_vendor', false)) {
            return collect();
        }

        $vendorPath = $this->getLanguagePath() . DIRECTORY_SEPARATOR . 'vendor';

        if (!$this->files->exists($vendorPath)) {
            return collect();
        }

        $packages = collect();
        $directories = $this->files->directories($vendorPath);

        foreach ($directories as $directory) {
            $packageName = basename($directory);
            $languages = $this->getVendorLanguages($packageName);

            $packages->push([
                'name' => $packageName,
                'path' => $directory,
                'languages_count' => $languages->count(),
                'languages' => $languages,
            ]);
        }

        return $packages;
    }
    protected function getLanguagePath(): string
    {
        return config('filament-translation-editor.language_path', base_path('lang'));
    }

    /**
     * Get languages for a vendor package
     */
    public function getVendorLanguages(string $package): Collection
    {

          $packagePath = $this->getLanguagePath() . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . $package;


        if (!$this->files->exists($packagePath)) {
            return collect();
        }

        $languages = collect();
        $directories = $this->files->directories($packagePath);

        foreach ($directories as $directory) {
            $languageCode = basename($directory);
            $phpFiles = $this->getVendorPhpFiles($package, $languageCode);

            $languages->push([
                'code' => $languageCode,
                'name' => ArrayHelper::getLanguageName($languageCode),
                'package' => $package,
                'php_files_count' => $phpFiles->count(),
                'directory_path' => $directory,
            ]);
        }

        return $languages;
    }

    /**
     * Get PHP files for vendor package language
     */
    public function getVendorPhpFiles(string $package, string $languageCode): Collection
    {
        $languageDir = $this->getLanguagePath() . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . $package . DIRECTORY_SEPARATOR . $languageCode;

        if (!$this->files->exists($languageDir)) {
            return collect();
        }

        $files = $this->files->files($languageDir);

        return collect($files)
            ->filter(function ($file) {
                return $file->getExtension() === 'php';
            })
            ->map(function ($file) use ($package, $languageCode) {
                return [
                    'filename' => $file->getFilenameWithoutExtension(),
                    'full_filename' => $file->getFilename(),
                    'path' => $file->getPathname(),
                    'package' => $package,
                    'language_code' => $languageCode,
                    'type' => 'php',
                    'size' => $file->getSize(),
                    'modified' => $file->getMTime(),
                ];
            })
            ->values();
    }

    /**
     * Read vendor translation file
     */
    public function readVendorTranslationFile(string $package, string $languageCode, string $filename): array
    {
        $filePath = $this->getVendorTranslationFilePath($package, $languageCode, $filename);

        if (!$this->files->exists($filePath)) {
            throw new \Exception("Vendor translation file not found: {$filePath}");
        }

        // Safely include the PHP file and return its array
        $translations = include $filePath;

        if (!is_array($translations)) {
            throw new \Exception("Invalid vendor translation file format. File must return an array: {$filePath}");
        }

        return $translations;
    }

    /**
     * Write vendor translation file
     */
    public function writeVendorTranslationFile(string $package, string $languageCode, string $filename, array $translations): bool
    {
        $filePath = $this->getVendorTranslationFilePath($package, $languageCode, $filename);

        // Generate PHP file content
        $phpContent = '<?php' . PHP_EOL . PHP_EOL . 'return ' . var_export($translations, true) . ';' . PHP_EOL;

        return $this->files->put($filePath, $phpContent) !== false;
    }

    /**
     * Get vendor translation file path
     */
    protected function getVendorTranslationFilePath(string $package, string $languageCode, string $filename): string
    {
        $filename = str_replace('.php', '', $filename); // Remove .php if present

        return $this->getLanguagePath() . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . $package . DIRECTORY_SEPARATOR . $languageCode . DIRECTORY_SEPARATOR . $filename . '.php';
    }
}
