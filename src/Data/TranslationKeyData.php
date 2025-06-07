<?php
/*
 * @package     :   Sepremex ManagerFile
 * @handler     :   Master Router
 * @developer   :   Ruben Mc
 * @contact     :   webcorm@gmail.com
 * @company     :   Sepremex | TranslationKeyData.php
 * @date        :   6/5/2025 | 15:14
*/

namespace Sepremex\FilamentTranslationEditor\Data;

class TranslationKeyData
{
    public function __construct(
        public string $key,
        public string $value,
    ) {}

    public static function fromArray(array $data): array
    {
        return collect($data)
            ->map(fn ($value, $key) => new self($key, $value))
            ->values()
            ->all();
    }

    public static function toArray(array $objects): array
    {
        return collect($objects)
            ->mapWithKeys(fn (self $item) => [$item->key => $item->value])
            ->all();
    }
}
