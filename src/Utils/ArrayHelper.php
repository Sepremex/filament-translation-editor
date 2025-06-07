<?php
/*
 * @package     :   Sepremex ManagerFile
 * @handler     :   Master Router
 * @developer   :   Ruben Mc
 * @contact     :   webcorm@gmail.com
 * @company     :   Sepremex | ArrayHelper.php
 * @date        :   6/4/2025 | 22:06
*/

namespace Sepremex\FilamentTranslationEditor\Utils;

class ArrayHelper
{
    /**
     * Flatten a multi-dimensional array using dot notation
     */
    public static function flatten(array $array, string $prefix = ''): array
    {
        $flattened = [];

        foreach($array as $key => $value) {
            $newKey = $prefix ? $prefix . '<~>' . $key : $key;

            if(is_array($value) && !empty($value)){
                $flattened = array_merge($flattened, static::flatten($value, $newKey));
            } else {
                $flattened[$newKey] = $value;
            }
        }

        return $flattened;
    }

    /**
     * Expand a flattened array back to multi-dimensional using dot notation
     */
    public static function expand(array $flattened): array
    {
        $expanded = [];

        foreach($flattened as $key => $value) {
            static::set($expanded, $key, $value);
        }

        return $expanded;
    }

    /**
     * Set a value in an array using dot notation
     */
    public static function set(array &$array, string $key, $value): void
    {
        $keys = explode('<~>', $key);
        $current = &$array;

        foreach($keys as $k) {
            if(!isset($current[$k]) || !is_array($current[$k])){
                $current[$k] = [];
            }
            $current = &$current[$k];
        }

        $current = $value;
    }

    /**
     * Get a value from an array using dot notation
     */
    public static function get(array $array, string $key, $default = null)
    {
        $keys = explode('<~>', $key);
        $current = $array;

        foreach($keys as $k) {
            if(!is_array($current) || !array_key_exists($k, $current)){
                return $default;
            }
            $current = $current[$k];
        }

        return $current;
    }

    /**
     * Check if a key exists in an array using dot notation
     */
    public static function has(array $array, string $key): bool
    {
        $keys = explode('<~>', $key);
        $current = $array;

        foreach($keys as $k) {
            if(!is_array($current) || !array_key_exists($k, $current)){
                return false;
            }
            $current = $current[$k];
        }

        return true;
    }

    /**
     * Remove a key from an array using dot notation
     */
    public static function forget(array &$array, string $key): void
    {
        $keys = explode('<~>', $key);
        $current = &$array;

        for($i = 0; $i < count($keys) - 1; $i++) {
            $k = $keys[$i];
            if(!isset($current[$k]) || !is_array($current[$k])){
                return;
            }
            $current = &$current[$k];
        }

        unset($current[end($keys)]);
    }

    /**
     * Get all keys from a nested array in dot notation
     */
    public static function getAllKeys(array $array, string $prefix = ''): array
    {
        $keys = [];

        foreach($array as $key => $value) {
            $newKey = $prefix ? $prefix . '<~>' . $key : $key;
            $keys[] = $newKey;

            if(is_array($value) && !empty($value)){
                $keys = array_merge($keys, static::getAllKeys($value, $newKey));
            }
        }

        return $keys;
    }

    /**
     * Get the depth of a nested array
     */
    public static function getDepth(array $array): int
    {
        $maxDepth = 0;

        foreach($array as $value) {
            if(is_array($value)){
                $depth = static::getDepth($value) + 1;
                $maxDepth = max($maxDepth, $depth);
            }
        }

        return $maxDepth;
    }

    /**
     * Filter array by keys using a callback or pattern
     */
    public static function filterKeys(array $array, $callback): array
    {
        if(is_string($callback)){
            $pattern = $callback;
            $callback = function($key) use ($pattern){
                return fnmatch($pattern, $key);
            };
        }

        return array_filter($array, $callback, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Merge two nested arrays recursively
     */
    public static function mergeRecursive(array $array1, array $array2): array
    {
        foreach($array2 as $key => $value) {
            if(isset($array1[$key]) && is_array($array1[$key]) && is_array($value)){
                $array1[$key] = static::mergeRecursive($array1[$key], $value);
            } else {
                $array1[$key] = $value;
            }
        }

        return $array1;
    }

    /**
     * Find differences between two arrays
     */
    public static function diff(array $array1, array $array2): array
    {
        $flattened1 = static::flatten($array1);
        $flattened2 = static::flatten($array2);

        $differences = ['added' => array_diff_key($flattened2, $flattened1), 'removed' => array_diff_key($flattened1, $flattened2), 'modified' => [],];

        // Find modified values
        foreach(array_intersect_key($flattened1, $flattened2) as $key => $value) {
            if($value !== $flattened2[$key]){
                $differences['modified'][$key] = ['old' => $value, 'new' => $flattened2[$key],];
            }
        }

        return $differences;
    }

    /**
     * Search for a value in a nested array and return the keys
     */
    public static function search(array $array, $searchValue, bool $strict = false): array
    {
        $results = [];
        $flattened = static::flatten($array);

        foreach($flattened as $key => $value) {
            if($strict ? $value === $searchValue : $value == $searchValue){
                $results[] = $key;
            }
        }

        return $results;
    }

    /**
     * Search for keys containing a specific string
     */
    public static function searchKeys(array $array, string $searchString, bool $caseSensitive = false): array
    {
        $results = [];
        $allKeys = static::getAllKeys($array);

        foreach($allKeys as $key) {
            $haystack = $caseSensitive ? $key : strtolower($key);
            $needle = $caseSensitive ? $searchString : strtolower($searchString);

            if(strpos($haystack, $needle) !== false){
                $results[] = $key;
            }
        }

        return $results;
    }

    /**
     * Replace values in an array based on a search term
     */
    public static function replaceValues(array $array, $search, $replace, bool $caseSensitive = false): array
    {
        $flattened = static::flatten($array);

        foreach($flattened as $key => $value) {
            if(is_string($value)){
                if($caseSensitive){
                    $flattened[$key] = str_replace($search, $replace, $value);
                } else {
                    $flattened[$key] = str_ireplace($search, $replace, $value);
                }
            }
        }

        return static::expand($flattened);
    }

    /**
     * Get a summary of the array structure
     */
    public static function getSummary(array $array): array
    {
        $flattened = static::flatten($array);

        return ['total_keys' => count($flattened), 'max_depth' => static::getDepth($array), 'empty_values' => count(array_filter($flattened, function($value){
            return empty($value) && $value !== '0';
        })), 'non_string_values' => count(array_filter($flattened, function($value){
            return !is_string($value);
        })), 'longest_key' => !empty($flattened) ? max(array_map('strlen', array_keys($flattened))) : 0, 'longest_value' => !empty($flattened) ? max(array_map(function($value){
            return is_string($value) ? strlen($value) : 0;
        }, $flattened)) : 0,];
    }

    /**
     * Validate array structure for translation files
     */
    public static function validateTranslationStructure(array $array): array
    {
        $errors = [];
        $flattened = static::flatten($array);

        foreach($flattened as $key => $value) {
            // Check for empty keys
            if(empty($key)){
                $errors[] = "Empty key found";
            }

            // Check for null values
            if(is_null($value)){
                $errors[] = "Null value found for key: {$key}";
            }

            // Check for numeric keys at root level
            if(is_numeric($key) && !str_contains($key, '<~>')){
                $errors[] = "Numeric key found at root level: {$key}";
            }

            // Check for very long keys
            if(strlen($key) > 255){
                $errors[] = "Key too long (>255 chars): {$key}";
            }
        }

        return $errors;
    }

    public static function arrayToPhpString(array $array, int $depth = 0): string
    {
        if (empty($array)) {
            return '[]';
        }

        $indent = str_repeat('    ', $depth);
        $nextIndent = str_repeat('    ', $depth + 1);

        $elements = [];

        foreach ($array as $key => $value) {
            $line = $nextIndent;
            $line .= "'" . addslashes($key) . "' => ";

            if (is_array($value)) {
                $line .= self::arrayToPhpString($value, $depth + 1);
            } else {
                $line .= "'" . addslashes($value) . "'";
            }

            $elements[] = $line;
        }

        return "[\n" . implode(",\n", $elements) . ",\n{$indent}]";
    }

    public static function getLanguageName(string $code): string
    {
        $names = [
            /* Idiomas principales because we can...*/
            'en' => 'English',
            'es' => 'Español',
            'fr' => 'Français',
            'de' => 'Deutsch',
            'it' => 'Italiano',
            'pt' => 'Português',
            'ru' => 'Русский',
            'ja' => '日本語',
            'ko' => '한국어',
            'zh' => '中文',
            'ar' => 'العربية',
            'hi' => 'हिन्दी',
            'nl' => 'Nederlands',
            'sv' => 'Svenska',
            'da' => 'Dansk',
            'no' => 'Norsk',
            'fi' => 'Suomi',
            'pl' => 'Polski',
            'cs' => 'Čeština',
            'sk' => 'Slovenčina',
            'hu' => 'Magyar',
            'tr' => 'Türkçe',
            'th' => 'ไทย',
            'vi' => 'Tiếng Việt',
            'id' => 'Bahasa Indonesia',
            'ms' => 'Bahasa Melayu',
            'tl' => 'Filipino',
            'he' => 'עברית',
            'el' => 'Ελληνικά',
            'bg' => 'Български',
            'ro' => 'Română',
            'hr' => 'Hrvatski',
            'sr' => 'Српски',
            'sl' => 'Slovenščina',
            'et' => 'Eesti',
            'lv' => 'Latviešu',
            'lt' => 'Lietuvių',
            'uk' => 'Українська',
            'be' => 'Беларуская',
            'ka' => 'ქართული',
            'hy' => 'Հայերեն',
            'az' => 'Azərbaycan',
            'kk' => 'Қазақша',
            'ky' => 'Кыргызча',
            'uz' => 'O\'zbek',
            'mn' => 'Монгол',
            'my' => 'မြန်မာ',
            'km' => 'ខ្មែរ',
            'lo' => 'ລາວ',
            'si' => 'සිංහල',
            'ta' => 'தமிழ்',
            'te' => 'తెలుగు',
            'ml' => 'മലയാളം',
            'kn' => 'ಕನ್ನಡ',
            'bn' => 'বাংলা',
            'gu' => 'ગુજરાતી',
            'pa' => 'ਪੰਜਾਬੀ',
            'or' => 'ଓଡ଼ିଆ',
            'as' => 'অসমীয়া',
            'ne' => 'नेपाली',
            'ur' => 'اردو',
            'fa' => 'فارسی',
            'ps' => 'پښتو',
            'ku' => 'Kurdî',
            'sw' => 'Kiswahili',
            'am' => 'አማርኛ',
            'zu' => 'isiZulu',
            'af' => 'Afrikaans',
            'sq' => 'Shqip',
            'eu' => 'Euskera',
            'ca' => 'Català',
            'gl' => 'Galego',
            'cy' => 'Cymraeg',
            'ga' => 'Gaeilge',
            'gd' => 'Gàidhlig',
            'is' => 'Íslenska',
            'fo' => 'Føroyskt',
            'mt' => 'Malti',
            'mk' => 'Македонски',
            'me' => 'Crnogorski',
            'bs' => 'Bosanski',
            'lb' => 'Lëtzebuergesch',

            /* Variantes regionales because why not!...*/
            'en-US' => 'English (US)',
            'en-GB' => 'English (UK)',
            'en-CA' => 'English (Canada)',
            'en-AU' => 'English (Australia)',
            'es-ES' => 'Español (España)',
            'es-MX' => 'Español (México)',
            'es-AR' => 'Español (Argentina)',
            'es-CO' => 'Español (Colombia)',
            'pt-BR' => 'Português (Brasil)',
            'pt-PT' => 'Português (Portugal)',
            'fr-FR' => 'Français (France)',
            'fr-CA' => 'Français (Canada)',
            'de-DE' => 'Deutsch (Deutschland)',
            'de-AT' => 'Deutsch (Österreich)',
            'de-CH' => 'Deutsch (Schweiz)',
            'it-IT' => 'Italiano (Italia)',
            'zh-CN' => '中文 (简体)',
            'zh-TW' => '中文 (繁體)',
            'zh-HK' => '中文 (香港)',
            'ar-SA' => 'العربية (السعودية)',
            'ar-EG' => 'العربية (مصر)',
            'ar-AE' => 'العربية (الإمارات)',
        ];

        return $names[$code] ?? ucfirst($code);
    }

}
