<?php
/*
 * @package     :   Sepremex ManagerFile
 * @handler     :   Master Router
 * @developer   :   Ruben Mc
 * @contact     :   webcorm@gmail.com
 * @company     :   Sepremex | MicrosoftTranslatorProvider.php
 * @date        :   6/9/2025 | 14:37
*/

namespace Sepremex\FilamentTranslationEditor\Services\Translation;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

class MicrosoftTranslatorProvider implements TranslationProviderInterface
{
    protected ?string $apiKey;
    protected string $region;
    protected string $endpoint;
    protected array $supportedLanguages = [];

    public function __construct(?string $apiKey = null, ?string $region = null, ?string $endpoint = null)
    {
        $this->apiKey = $apiKey ?: config('filament-translation-editor.auto_translate.providers.microsoft.api_key');
        $this->region = $region ?: config('filament-translation-editor.auto_translate.providers.microsoft.region', 'global');
        $this->endpoint = $endpoint ?: config('filament-translation-editor.auto_translate.providers.microsoft.endpoint', 'https://api.cognitive.microsofttranslator.com');

        // Ensure endpoint doesn't have trailing slash
        $this->endpoint = rtrim($this->endpoint, '/');
    }

    public function translate(string $text, string $from, string $to): string
    {
        // Always log the translation attempt
        \Log::debug("Microsoft Translator: Attempting to translate '{$text}' from {$from} to {$to}");

        if (!$this->isAvailable()) {
            \Log::warning("Microsoft Translator: API not available - API key missing or invalid. Returning original text.");
            return $text; // Return original text instead of throwing exception
        }

        if (!$this->supportsLanguagePair($from, $to)) {
            \Log::warning("Microsoft Translator: Language pair {$from} -> {$to} not supported. Returning original text.");
            return $text; // Return original text instead of throwing exception
        }

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Ocp-Apim-Subscription-Key' => $this->apiKey,
                    'Ocp-Apim-Subscription-Region' => $this->region,
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->endpoint}/translate", [
                    'api-version' => '3.0',
                    'from' => $from,
                    'to' => [$to],
                ], [
                    [
                        'text' => $text
                    ]
                ]);

            if (!$response->successful()) {
                $error = $response->json('error.message', 'Unknown error');
                $statusCode = $response->status();

                // Log different types of API errors
                if ($statusCode === 401) {
                    \Log::error("Microsoft Translator: Invalid API key or subscription expired. Returning original text. Error: {$error}");
                } elseif ($statusCode === 403) {
                    \Log::error("Microsoft Translator: API quota exceeded or access denied. Returning original text. Error: {$error}");
                } elseif ($statusCode === 429) {
                    \Log::warning("Microsoft Translator: Rate limit exceeded. Returning original text. Error: {$error}");
                } else {
                    \Log::error("Microsoft Translator: API error (HTTP {$statusCode}). Returning original text. Error: {$error}");
                }

                return $text; // Return original text instead of throwing exception
            }

            $data = $response->json();

            if (empty($data) || !isset($data[0]['translations'][0]['text'])) {
                \Log::error("Microsoft Translator: Invalid response structure. Returning original text. Response: " . json_encode($data));
                return $text; // Return original text instead of throwing exception
            }

            $translatedText = $data[0]['translations'][0]['text'];

            // Log successful translation
            \Log::info("Microsoft Translator: Successfully translated '{$text}' to '{$translatedText}' ({$from} -> {$to})");

            return $translatedText;

        } catch (RequestException $e) {
            \Log::error("Microsoft Translator: Network error - failed to connect. Returning original text. Error: " . $e->getMessage());
            return $text; // Return original text instead of throwing exception
        } catch (\Exception $e) {
            \Log::error("Microsoft Translator: Unexpected error. Returning original text. Error: " . $e->getMessage());
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
                ->withHeaders([
                    'Ocp-Apim-Subscription-Key' => $this->apiKey,
                    'Ocp-Apim-Subscription-Region' => $this->region,
                ])
                ->get("{$this->endpoint}/languages", [
                    'api-version' => '3.0',
                    'scope' => 'translation'
                ]);

            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getName(): string
    {
        return 'microsoft';
    }

    public function supportsLanguagePair(string $from, string $to): bool
    {
        $supportedLanguages = $this->getSupportedLanguages();

        return in_array($from, $supportedLanguages) && in_array($to, $supportedLanguages);
    }

    /**
     * Load supported languages from Microsoft Translator API
     */
    protected function loadSupportedLanguages(): void
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Ocp-Apim-Subscription-Key' => $this->apiKey,
                    'Ocp-Apim-Subscription-Region' => $this->region,
                ])
                ->get("{$this->endpoint}/languages", [
                    'api-version' => '3.0',
                    'scope' => 'translation'
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->supportedLanguages = array_keys($data['translation'] ?? []);
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
            'en', 'es', 'fr', 'de', 'it', 'pt', 'ru', 'ja', 'ko', 'zh-hans', 'zh-hant',
            'ar', 'hi', 'tr', 'pl', 'nl', 'sv', 'da', 'no', 'fi', 'el', 'he', 'th',
            'vi', 'uk', 'cs', 'sk', 'ro', 'bg', 'hr', 'sl', 'et', 'lv', 'lt', 'hu',
            'mt', 'ga', 'cy', 'eu', 'ca', 'gl', 'is', 'mk', 'sq', 'az', 'be', 'bn',
            'bs', 'fa', 'fil', 'fj', 'gu', 'ht', 'id', 'kk', 'km', 'kn', 'ky', 'lo',
            'mg', 'ml', 'mr', 'ms', 'my', 'ne', 'or', 'ps', 'pa', 'sm', 'si', 'so',
            'sw', 'ta', 'te', 'to', 'ty', 'ur', 'uz', 'zu'
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
                'region' => $this->region,
                'error' => 'API key not configured or invalid',
                'languages_count' => 0,
            ];
        }

        try {
            // Get account info if available
            $response = Http::timeout(5)
                ->withHeaders([
                    'Ocp-Apim-Subscription-Key' => $this->apiKey,
                    'Ocp-Apim-Subscription-Region' => $this->region,
                ])
                ->get("{$this->endpoint}/languages", [
                    'api-version' => '3.0',
                    'scope' => 'translation'
                ]);

            return [
                'available' => true,
                'endpoint' => $this->endpoint,
                'region' => $this->region,
                'api_version' => '3.0',
                'languages_count' => count($this->getSupportedLanguages()),
                'free_tier' => '2M characters/month',
                'cost' => '$10 per 1M characters after free tier',
            ];
        } catch (\Exception $e) {
            return [
                'available' => false,
                'endpoint' => $this->endpoint,
                'region' => $this->region,
                'error' => $e->getMessage(),
                'languages_count' => 0,
            ];
        }
    }

    /**
     * Get available service endpoints for this provider
     */
    public function getAvailableServiceEndpoints(): array
    {
        return [
            'global' => [
                'endpoint' => 'https://api.cognitive.microsofttranslator.com',
                'region' => 'global',
                'name' => 'Global Endpoint',
                'description' => 'Multi-region global endpoint',
            ],
            'eastus' => [
                'endpoint' => 'https://eastus.api.cognitive.microsofttranslator.com',
                'region' => 'eastus',
                'name' => 'East US',
                'description' => 'East US specific endpoint',
            ],
            'westus' => [
                'endpoint' => 'https://westus.api.cognitive.microsofttranslator.com',
                'region' => 'westus',
                'name' => 'West US',
                'description' => 'West US specific endpoint',
            ],
            'westeurope' => [
                'endpoint' => 'https://westeurope.api.cognitive.microsofttranslator.com',
                'region' => 'westeurope',
                'name' => 'West Europe',
                'description' => 'West Europe specific endpoint',
            ],
            'eastasia' => [
                'endpoint' => 'https://eastasia.api.cognitive.microsofttranslator.com',
                'region' => 'eastasia',
                'name' => 'East Asia',
                'description' => 'East Asia specific endpoint',
            ],
        ];
    }

    /**
     * Build the correct API URL with parameters
     */
    protected function buildTranslateUrl(string $from, string $to): string
    {
        $params = http_build_query([
            'api-version' => '3.0',
            'from' => $from,
            'to' => $to,
        ]);

        return "{$this->endpoint}/translate?{$params}";
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
                ->withHeaders([
                    'Ocp-Apim-Subscription-Key' => $this->apiKey,
                    'Ocp-Apim-Subscription-Region' => $this->region,
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->endpoint}/detect?api-version=3.0", [
                    [
                        'text' => $text
                    ]
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data[0]['language'] ?? null;
            }
        } catch (\Exception $e) {
            \Log::warning("Microsoft language detection failed: " . $e->getMessage());
        }

        return null;
    }
}
