<?php
/*
 * @package     :   Sepremex ManagerFile
 * @handler     :   Master Router
 * @developer   :   Ruben Mc
 * @contact     :   webcorm@gmail.com
 * @company     :   Sepremex | InvalidLanguagePathException.php
 * @date        :   6/4/2025 | 22:06
*/

namespace Sepremex\FilamentTranslationEditor\Exceptions;

use Exception;

class InvalidLanguagePathException extends Exception
{
    public function __construct(string $path)
    {
        parent::__construct("Invalid language path: '{$path}' does not exist or is not readable.");
    }
}
