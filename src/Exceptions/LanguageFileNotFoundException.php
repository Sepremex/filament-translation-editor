<?php
/*
 * @package     :   Sepremex ManagerFile
 * @handler     :   Master Router
 * @developer   :   Ruben Mc
 * @contact     :   webcorm@gmail.com
 * @company     :   Sepremex | LanguageFileNotFoundException.php
 * @date        :   6/4/2025 | 22:06
*/

namespace Sepremex\FilamentTranslationEditor\Exceptions;

use Exception;

class LanguageFileNotFoundException extends Exception
{
    public function __construct(string $language, string $file)
    {
        parent::__construct("Language file not found: '{$language}/{$file}'.");
    }
}
