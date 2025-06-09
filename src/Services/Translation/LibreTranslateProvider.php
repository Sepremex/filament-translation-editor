<?php
/*
 * @package     :   Sepremex ManagerFile
 * @handler     :   Master Router
 * @developer   :   Ruben Mc
 * @contact     :   webcorm@gmail.com
 * @company     :   Sepremex | LibreTranslateProvider.php
 * @date        :   6/9/2025 | 14:37
*/

namespace Sepremex\FilamentTranslationEditor\Services\Translation;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

class LibreTranslateProvider implements TranslationProviderInterface
{
    protected string $baseUrl;
    protected ?string $apiKey;
    protected array $supportedLanguages = [];

    public function __construct(?string $baseUrl = null, ?string $apiKey = null)
    {
        $this->baseUrl = $baseUrl ?: $this->getDefaultUrl();
        $this->apiKey = $apiKey ?: config('filament-translation-editor.auto_translate.providers.libretranslate.api_key');
    }

    public function translate(string $text, string $from, string $to): string
    {
        // Always log the translation attempt
        \Log::debug("LibreTranslate: Attempting to translate '{$text}' from {$from} to {$to} using {$this->baseUrl}");

        if (!$this->isAvailable()) {
            \Log::warning("LibreTranslate: Service not available at {$this->baseUrl}. Returning original text.");
            return $text; // Return original text instead of throwing exception
        }

        if (!$this->supportsLanguagePair($from, $to)) {
            \Log::warning("LibreTranslate: Language pair {$from} -> {$to} not supported. Returning original text.");
            return $text; // Return original text instead of throwing exception
        }

        try {
            $requestData = array_filter([
                'q' => $text,
                'source' => $from,
                'target' => $to,
                'api_key' => $this->apiKey,
            ]);

            $response = Http::timeout(30)->post("{$this->baseUrl}/translate", $requestData);

            if (!$response->successful()) {
                $statusCode = $response->status();
                $errorBody = $response->body();

                // Log different types of API errors
                if ($statusCode === 400) {
                    \Log::warning("LibreTranslate: Bad request (unsupported language pair or invalid text). Returning original text. Response: {$errorBody}");
                } elseif ($statusCode === 403) {
                    \Log::error("LibreTranslate: Access denied (invalid API key or quota exceeded). Returning original text. Response: {$errorBody}");
                } elseif ($statusCode === 429) {
                    \Log::warning("LibreTranslate: Rate limit exceeded. Returning original text. Response: {$errorBody}");
                } elseif ($statusCode === 500) {
                    \Log::error("LibreTranslate: Server error. Returning original text. Response: {$errorBody}");
                } else {
                    \Log::error("LibreTranslate: API error (HTTP {$statusCode}). Returning original text. Response: {$errorBody}");
                }

                return $text; // Return original text instead of throwing exception
            }

            $data = $response->json();

            if (!isset($data['translatedText'])) {
                \Log::error("LibreTranslate: Invalid response structure. Returning original text. Response: " . json_encode($data));
                return $text; // Return original text instead of throwing exception
            }

            $translatedText = $data['translatedText'];

            // Log successful translation
            \Log::info("LibreTranslate: Successfully translated '{$text}' to '{$translatedText}' ({$from} -> {$to})");

            return $translatedText;

        } catch (RequestException $e) {
            \Log::error("LibreTranslate: Network error - failed to connect to {$this->baseUrl}. Returning original text. Error: " . $e->getMessage());
            return $text; // Return original text instead of throwing exception
        } catch (\Exception $e) {
            \Log::error("LibreTranslate: Unexpected error. Returning original text. Error: " . $e->getMessage());
            return $text; // Return original text instead of throwing exception
        }
    }

    public function getSupportedLanguages(): array
    {
        if (empty($this->supportedLanguages)) {
            $this->loadSupportedLanguages();
        }

        return $this->supportedLanguages;
    }

    public function isAvailable(): bool
    {
        try {
            $response = Http::timeout(5)->get("{$this->baseUrl}/languages");
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getName(): string
    {
        return 'libretranslate';
    }

    public function supportsLanguagePair(string $from, string $to): bool
    {
        $supportedLanguages = $this->getSupportedLanguages();

        return in_array($from, $supportedLanguages) && in_array($to, $supportedLanguages);
    }

    /**
     * Load supported languages from LibreTranslate API
     */
    protected function loadSupportedLanguages(): void
    {
        try {
            $response = Http::timeout(10)->get("{$this->baseUrl}/languages");

            if ($response->successful()) {
                $languages = $response->json();
                $this->supportedLanguages = collect($languages)
                    ->pluck('code')
                    ->toArray();
            } else {
                // Fallback to common languages if API fails
                $this->supportedLanguages = $this->getFallbackLanguages();
            }
        } catch (\Exception $e) {
            // Fallback to common languages if API fails
            $this->supportedLanguages = $this->getFallbackLanguages();
        }
    }

    /**
     * Get fallback languages when API is not available
     */
    protected function getFallbackLanguages(): array
    {
        return [
            'en', 'es', 'fr', 'de', 'it', 'pt', 'ru', 'ja', 'ko', 'zh',
            'ar', 'hi', 'tr', 'pl', 'nl', 'sv', 'da', 'no', 'fi', 'el'
        ];
    }

    /**
     * Get default URL based on configuration with fallbacks
     */
    protected function getDefaultUrl(): string
    {
        $configUrl = config('filament-translation-editor.auto_translate.providers.libretranslate.url');

        if ($configUrl) {
            return rtrim($configUrl, '/'); // Remove trailing slash
        }

        // Fallback URLs in order of preference
        $fallbackUrls = [
            'http://localhost:5000',           // Local Docker (most common for dev)
            'https://libretranslate.com',      // Official hosted service
            'https://translate.argosopentech.com', // Alternative hosted service
        ];

        // Try to find a working URL
        foreach ($fallbackUrls as $url) {
            if ($this->testConnection($url)) {
                \Log::info("LibreTranslate: Auto-detected working service at {$url}");
                return $url;
            }
        }

        // Default to localhost if nothing works
        \Log::warning("LibreTranslate: No working service found, defaulting to localhost:5000");
        return 'http://localhost:5000';
    }

    /**
     * Test if a URL is reachable
     */
    protected function testConnection(string $url): bool
    {
        try {
            $response = Http::timeout(3)->get("{$url}/languages");
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Test the connection and get provider info
     */
    public function getProviderInfo(): array
    {
        try {
            $isAvailable = $this->isAvailable();
            $info = [
                'available' => $isAvailable,
                'url' => $this->baseUrl,
                'api_key_required' => !empty($this->apiKey),
                'languages_count' => 0,
            ];

            if ($isAvailable) {
                $response = Http::timeout(5)->get("{$this->baseUrl}/frontend/settings");

                $info['version'] = $response->json('version', 'unknown');
                $info['languages_count'] = count($this->getSupportedLanguages());
                $info['cost'] = 'Free (self-hosted) or freemium (hosted)';
            } else {
                $info['error'] = "Unable to connect to {$this->baseUrl}";
            }

            return $info;
        } catch (\Exception $e) {
            return [
                'available' => false,
                'url' => $this->baseUrl,
                'error' => $e->getMessage(),
                'languages_count' => 0,
            ];
        }
    }
}
