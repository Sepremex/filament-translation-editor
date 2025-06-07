<?php
/*
 * @package     :   Sepremex ManagerFile
 * @handler     :   Master Router
 * @developer   :   Ruben Mc
 * @contact     :   webcorm@gmail.com
 * @company     :   Sepremex | LanguageFileWriter.php
 * @date        :   6/4/2025 | 21:59
*/

namespace Sepremex\FilamentTranslationEditor\Services;

use Illuminate\Filesystem\Filesystem;
use Sepremex\FilamentTranslationEditor\Utils\ArrayBuilder;
use Sepremex\FilamentTranslationEditor\Utils\ArrayHelper;

class LanguageFileWriter
{
    public function __construct(
        protected Filesystem $files,
        protected ArrayBuilder $builder,
    ) {}

    public function write(string $language, string $filename, array $data): bool
    {
        $basePath = base_path(config('filament-translation-editor.path', 'lang'));

        $rawDataWorked = $this->builder->expand($data);

        // JSON file
        if (str_ends_with($filename, '.json') || $filename === '__json') {
            $path = $basePath . DIRECTORY_SEPARATOR . $language . '.json';
            $json = json_encode($rawDataWorked, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            return $this->files->put($path, $json) !== false;
        }
        // PHP file
        $path = $basePath . DIRECTORY_SEPARATOR . $language . DIRECTORY_SEPARATOR . $filename.'.php';

        $phpContent = "<?php\n\nreturn " . ArrayHelper::arrayToPhpString($rawDataWorked, 0) . ";\n";

        //$phpContent = '<?php' . PHP_EOL . PHP_EOL . 'return ' . var_export($array, true) . ';' . PHP_EOL;

        return $this->files->put($path, $phpContent) !== false;
    }
}
