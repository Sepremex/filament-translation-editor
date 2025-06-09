<?php
/*
 * @package     :   Sepremex ManagerFile
 * @handler     :   Master Router
 * @developer   :   Ruben Mc
 * @contact     :   webcorm@gmail.com
 * @company     :   Sepremex | GoogleTranslateProvider.php
 * @date        :   6/9/2025 | 16:30
*/

namespace Sepremex\FilamentTranslationEditor\Services\Translation;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

class GoogleTranslateProvider implements TranslationProviderInterface
{
    protected ?string $apiKey;
    protected string $endpoint;
    protected array $supportedLanguages = [];

    public function __construct(?string $apiKey = null, ?string $endpoint = null)
    {
        $this->apiKey = $apiKey ?: config('filament-translation-editor.auto_translate.providers.google.api_key');
        $this->endpoint = $endpoint ?: config('filament-translation-editor.auto_translate.providers.google.endpoint', 'https://translation.googleapis.com');

        // Ensure endpoint doesn't have trailing slash
        $this->endpoint = rtrim($this->endpoint, '/');
    }

    public function translate(string $text, string $from, string $to): string
    {
        // Always log the translation attempt
        \Log::debug("Google Translate: Attempting to translate '{$text}' from {$from} to {$to} using {$this->endpoint}");

        if (!$this->isAvailable()) {
            \Log::warning("Google Translate: API not available - API key missing or invalid. Returning original text.");
            return $text; // Return original text instead of throwing exception
        }

        if (!$this->supportsLanguagePair($from, $to)) {
            \Log::warning("Google Translate: Language pair {$from} -> {$to} not supported. Returning original text.");
            return $text; // Return original text instead of throwing exception
        }

        try {
            $response = Http::timeout(30)
                ->post("{$this->endpoint}/language/translate/v2", [
                    'key' => $this->apiKey,
                    'q' => $text,
                    'source' => $from,
                    'target' => $to,
                    'format' => 'text',
                ]);

            if (!$response->successful()) {
                $statusCode = $response->status();
                $errorData = $response->json();
                $errorMessage = $errorData['error']['message'] ?? 'Unknown error';

                // Log different types of API errors
                if ($statusCode === 400) {
                    \Log::warning("Google Translate: Bad request (invalid parameters or unsupported language). Returning original text. Error: {$errorMessage}");
                } elseif ($statusCode === 401) {
                    \Log::error("Google Translate: Invalid API key or authentication failed. Returning original text. Error: {$errorMessage}");
                } elseif ($statusCode === 403) {
                    \Log::error("Google Translate: Access denied or quota exceeded. Returning original text. Error: {$errorMessage}");
                } elseif ($statusCode === 429) {
                    \Log::warning("Google Translate: Rate limit exceeded. Returning original text. Error: {$errorMessage}");
                } else {
                    \Log::error("Google Translate: API error (HTTP {$statusCode}). Returning original text. Error: {$errorMessage}");
                }

                return $text; // Return original text instead of throwing exception
            }

            $data = $response->json();

            if (!isset($data['data']['translations'][0]['translatedText'])) {
                \Log::error("Google Translate: Invalid response structure. Returning original text. Response: " . json_encode($data));
                return $text; // Return original text instead of throwing exception
            }

            $translatedText = $data['data']['translations'][0]['translatedText'];

            // Decode HTML entities that Google might return
            $translatedText = html_entity_decode($translatedText, ENT_QUOTES | ENT_HTML5, 'UTF-8');

            // Log successful translation
            \Log::info("Google Translate: Successfully translated '{$text}' to '{$translatedText}' ({$from} -> {$to})");

            return $translatedText;

        } catch (RequestException $e) {
            \Log::error("Google Translate: Network error - failed to connect to {$this->endpoint}. Returning original text. Error: " . $e->getMessage());
            return $text; // Return original text instead of throwing exception
        } catch (\Exception $e) {
            \Log::error("Google Translate: Unexpected error. Returning original text. Error: " . $e->getMessage());
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
        if (empty($this->apiKey)) {
            return false;
        }

        try {
            $response = Http::timeout(5)
                ->get("{$this->endpoint}/language/translate/v2/languages", [
                    'key' => $this->apiKey,
                ]);

            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getName(): string
    {
        return 'google';
    }

    public function supportsLanguagePair(string $from, string $to): bool
    {
        $supportedLanguages = $this->getSupportedLanguages();

        return in_array($from, $supportedLanguages) && in_array($to, $supportedLanguages);
    }

    /**
     * Load supported languages from Google Translate API
     */
    protected function loadSupportedLanguages(): void
    {
        try {
            $response = Http::timeout(10)
                ->get("{$this->endpoint}/language/translate/v2/languages", [
                    'key' => $this->apiKey,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $languages = $data['data']['languages'] ?? [];
                $this->supportedLanguages = array_column($languages, 'language');
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
            'en', 'es', 'fr', 'de', 'it', 'pt', 'ru', 'ja', 'ko', 'zh', 'zh-cn', 'zh-tw',
            'ar', 'hi', 'tr', 'pl', 'nl', 'sv', 'da', 'no', 'fi', 'el', 'he', 'th', 'vi',
            'uk', 'cs', 'sk', 'ro', 'bg', 'hr', 'sl', 'et', 'lv', 'lt', 'hu', 'mt', 'ga',
            'cy', 'eu', 'ca', 'gl', 'is', 'mk', 'sq', 'az', 'be', 'bn', 'bs', 'fa', 'fil',
            'fj', 'gu', 'ht', 'id', 'kk', 'km', 'kn', 'ky', 'lo', 'mg', 'ml', 'mr', 'ms',
            'my', 'ne', 'or', 'ps', 'pa', 'sm', 'si', 'so', 'sw', 'ta', 'te', 'to', 'ty',
            'ur', 'uz', 'zu', 'af', 'am', 'co', 'eo', 'fy', 'gd', 'haw', 'hmn', 'ig', 'jw',
            'kk', 'ku', 'la', 'lb', 'mi', 'mn', 'ny', 'sd', 'sn', 'st', 'su', 'tg', 'tl',
            'xh', 'yi', 'yo'
        ];
    }

    /**
     * Get provider info and usage statistics
     */
    public function getProviderInfo(): array
    {
        if (!$this->isAvailable()) {
            return [
                'available' => false,
                'endpoint' => $this->endpoint,
                'error' => 'API key not configured or invalid',
                'languages_count' => 0,
            ];
        }

        try {
            $response = Http::timeout(5)
                ->get("{$this->endpoint}/language/translate/v2/languages", [
                    'key' => $this->apiKey,
                ]);

            return [
                'available' => true,
                'endpoint' => $this->endpoint,
                'api_version' => 'v2',
                'languages_count' => count($this->getSupportedLanguages()),
                'free_tier' => 'None (paid service)',
                'cost' => '$20 per 1M characters',
                'features' => ['Translation', 'Language Detection', 'HTML Support'],
            ];
        } catch (\Exception $e) {
            return [
                'available' => false,
                'endpoint' => $this->endpoint,
                'error' => $e->getMessage(),
                'languages_count' => 0,
            ];
        }
    }

    /**
     * Detect language of given text
     */
    public function detectLanguage(string $text): ?string
    {
        if (!$this->isAvailable()) {
            return null;
        }

        try {
            $response = Http::timeout(10)
                ->post("{$this->endpoint}/language/translate/v2/detect", [
                    'key' => $this->apiKey,
                    'q' => $text,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $detections = $data['data']['detections'][0] ?? [];

                if (!empty($detections)) {
                    return $detections[0]['language'] ?? null;
                }
            }
        } catch (\Exception $e) {
            \Log::warning("Google language detection failed: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Get available service endpoints (rarely changed for Google)
     */
    public function getAvailableServiceEndpoints(): array
    {
        return [
            'default' => [
                'endpoint' => 'https://translation.googleapis.com',
                'name' => 'Default Google Translate API',
                'description' => 'Standard Google Cloud Translation API endpoint',
                'version' => 'v2',
            ],
            'advanced' => [
                'endpoint' => 'https://translate.googleapis.com',
                'name' => 'Advanced Translation API',
                'description' => 'Google Cloud Translation API v3 (Advanced)',
                'version' => 'v3',
                'note' => 'Requires different implementation',
            ],
        ];
    }
}
