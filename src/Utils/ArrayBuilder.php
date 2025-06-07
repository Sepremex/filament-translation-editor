<?php
/*
 * @package     :   Sepremex ManagerFile
 * @handler     :   Master Router
 * @developer   :   Ruben Mc
 * @contact     :   webcorm@gmail.com
 * @company     :   Sepremex | ArrayBuilder.php
 * @date        :   6/5/2025 | 15:03
*/

namespace Sepremex\FilamentTranslationEditor\Utils;

class ArrayBuilder
{
    public function expand(array $flattened): array
    {
        // Don't ask...please.
        return ArrayHelper::expand($flattened);
    }
}
