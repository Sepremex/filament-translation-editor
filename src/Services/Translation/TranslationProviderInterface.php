<?php
/*
 * @package     :   Sepremex ManagerFile
 * @handler     :   Master Router
 * @developer   :   Ruben Mc
 * @contact     :   webcorm@gmail.com
 * @company     :   Sepremex | TranslationProviderInterface.php
 * @date        :   6/9/2025 | 14:37
*/

namespace Sepremex\FilamentTranslationEditor\Services\Translation;

interface TranslationProviderInterface
{
    /**
     * Translate text from source language to target language
     *
     * @param string $text Text to translate
     * @param string $from Source language code (e.g., 'en')
     * @param string $to Target language code (e.g., 'es')
     * @return string Translated text
     * @throws \Exception If translation fails
     */
    public function translate(string $text, string $from, string $to): string;

    /**
     * Get supported language codes for this provider
     *
     * @return array Array of supported language codes
     * @throws \Exception If unable to fetch supported languages
     */
    public function getSupportedLanguages(): array;

    /**
     * Check if the provider is available and properly configured
     *
     * @return bool True if provider is ready to use
     */
    public function isAvailable(): bool;

    /**
     * Get the provider name/identifier
     *
     * @return string Provider name (e.g., 'libretranslate', 'microsoft')
     */
    public function getName(): string;

    /**
     * Validate if a language pair is supported
     *
     * @param string $from Source language code
     * @param string $to Target language code
     * @return bool True if translation between these languages is supported
     */
    public function supportsLanguagePair(string $from, string $to): bool;
}
