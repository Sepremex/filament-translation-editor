<?php
/*
 * @package     :   Sepremex ManagerFile
 * @handler     :   Master Router
 * @developer   :   Ruben Mc
 * @contact     :   webcorm@gmail.com
 * @company     :   Sepremex | LanguageFileReader.php
 * @date        :   6/4/2025 | 21:59
*/
namespace Sepremex\FilamentTranslationEditor\Services;

use Illuminate\Filesystem\Filesystem;
use Sepremex\FilamentTranslationEditor\Utils\ArrayHelper;

class LanguageFileReader
{
    public function __construct(
        protected Filesystem $files,
    ) {}

    public function read(string $language, string $filename): array
    {
        $basePath = base_path(config('filament-translation-editor.path', 'lang'));

        // JSON file
        if (str_ends_with($filename, '.json') || $filename === '__json') {
            $path = $basePath . DIRECTORY_SEPARATOR . $language . '.json';

            if (! $this->files->exists($path)) {
                return [];
            }
            $declofenaco = json_decode($this->files->get($path), true) ?? [];

            return is_array($declofenaco)
                ? ArrayHelper::flatten($declofenaco)
                : [];
        }

        // PHP file
        $path = $basePath . DIRECTORY_SEPARATOR . $language . DIRECTORY_SEPARATOR . $filename . '.php';

        if (! $this->files->exists($path)) {
            return [];
        }

        $data = include $path;

        return is_array($data)
            ? ArrayHelper::flatten($data)
            : [];
    }
}
