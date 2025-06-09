<?php
/*
 * @package     :   Sepremex ManagerFile
 * @handler     :   Master Router
 * @developer   :   Ruben Mc
 * @contact     :   webcorm@gmail.com
 * @company     :   Sepremex | TranslationManager.php
 * @date        :   6/9/2025 | 14:38
*/

namespace Sepremex\FilamentTranslationEditor\Services\Translation;

class TranslationManager
{
    protected array $providers = [];
    protected ?TranslationProviderInterface $defaultProvider = null;

    public function __construct()
    {
        $this->registerDefaultProviders();
    }

    /**
     * Register a translation provider
     */
    public function registerProvider(string $name, TranslationProviderInterface $provider): void
    {
        $this->providers[$name] = $provider;
    }

    /**
     * Get a specific provider by name
     */
    public function getProvider(string $name): ?TranslationProviderInterface
    {
        return $this->providers[$name] ?? null;
    }

    /**
     * Get the default/configured provider
     */
    public function getDefaultProvider(): ?TranslationProviderInterface
    {
        if ($this->defaultProvider) {
            return $this->defaultProvider;
        }

        $defaultName = config('filament-translation-editor.auto_translate.provider', 'libretranslate');
        $provider = $this->getProvider($defaultName);

        if ($provider && $provider->isAvailable()) {
            $this->defaultProvider = $provider;
            return $provider;
        }

        // Fallback: find first available provider
        foreach ($this->providers as $provider) {
            if ($provider->isAvailable()) {
                $this->defaultProvider = $provider;
                return $provider;
            }
        }

        return null;
    }

    /**
     * Translate text using the default provider
     */
    public function translate(string $text, string $from, string $to): string
    {
        $provider = $this->getDefaultProvider();

        if (!$provider) {
            throw new \Exception('No translation provider available');
        }

        return $provider->translate($text, $from, $to);
    }

    /**
     * Translate text using a specific provider
     */
    public function translateWith(string $providerName, string $text, string $from, string $to): string
    {
        $provider = $this->getProvider($providerName);

        if (!$provider) {
            throw new \Exception("Translation provider '{$providerName}' not found");
        }

        if (!$provider->isAvailable()) {
            throw new \Exception("Translation provider '{$providerName}' is not available");
        }

        return $provider->translate($text, $from, $to);
    }

    /**
     * Check if auto-translation is enabled and available
     */
    public function isAutoTranslationEnabled(): bool
    {
        $enabled = config('filament-translation-editor.auto_translate.enabled', false);

        return $enabled && $this->getDefaultProvider() !== null;
    }

    /**
     * Get all registered providers with their status
     */
    public function getProvidersStatus(): array
    {
        $status = [];

        foreach ($this->providers as $name => $provider) {
            $status[$name] = [
                'name' => $provider->getName(),
                'available' => $provider->isAvailable(),
                'supported_languages_count' => count($provider->getSupportedLanguages()),
                'is_default' => $name === config('filament-translation-editor.auto_translate.provider'),
            ];
        }

        return $status;
    }

    /**
     * Get supported languages for the default provider
     */
    public function getSupportedLanguages(): array
    {
        $provider = $this->getDefaultProvider();

        return $provider ? $provider->getSupportedLanguages() : [];
    }

    /**
     * Check if a language pair is supported by the default provider
     */
    public function supportsLanguagePair(string $from, string $to): bool
    {
        $provider = $this->getDefaultProvider();

        return $provider ? $provider->supportsLanguagePair($from, $to) : false;
    }

    /**
     * Translate to multiple target languages at once
     */
    public function translateToMultiple(string $text, string $from, array $targetLanguages): array
    {
        $provider = $this->getDefaultProvider();

        if (!$provider) {
            throw new \Exception('No translation provider available');
        }

        $translations = [];
        $errors = [];

        foreach ($targetLanguages as $to) {
            try {
                if ($provider->supportsLanguagePair($from, $to)) {
                    $translations[$to] = $provider->translate($text, $from, $to);
                } else {
                    $errors[$to] = "Language pair {$from} -> {$to} not supported";
                }
            } catch (\Exception $e) {
                $errors[$to] = $e->getMessage();
            }
        }

        return [
            'translations' => $translations,
            'errors' => $errors,
        ];
    }

    /**
     * Register default providers based on configuration
     */
    protected function registerDefaultProviders(): void
    {
        if (config('filament-translation-editor.auto_translate.providers.libretranslate')) {
            $this->registerProvider('libretranslate', new LibreTranslateProvider());
        }
         if (config('filament-translation-editor.auto_translate.providers.microsoft')) {
             $this->registerProvider('microsoft', new MicrosoftTranslatorProvider());
         }

         if (config('filament-translation-editor.auto_translate.providers.google')) {
             $this->registerProvider('microsoft', new GoogleTranslateProvider());
         }

         // TODO add more providers...
        // FIXME lets make this dynamic XD
    }

    /**
     * Get available providers (only those that are currently working)
     */
    public function getAvailableProviders(): array
    {
        return array_filter($this->providers, function ($provider) {
            return $provider->isAvailable();
        });
    }

    /**
     * Force refresh of default provider (useful if config changes)
     */
    public function refreshDefaultProvider(): void
    {
        $this->defaultProvider = null;
    }
}
