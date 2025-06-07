<?php
/*
 * @package     :   Sepremex ManagerFile
 * @handler     :   Master Router
 * @developer   :   Ruben Mc
 * @contact     :   webcorm@gmail.com
 * @company     :   Sepremex | ManageVendorPackages.php
 * @date        :   6/6/2025 | 17:53
*/

namespace Sepremex\FilamentTranslationEditor\Pages;

use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Model;

class ManageVendorPackages extends Page
{
    protected static string $view = 'filament-translation-editor::pages.manage-vendor-packages';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public function getTitle(): string
    {
        return 'Vendor Packages';
    }


    public static function getSlug(): string
    {
        $baseRoute = config('filament-translation-editor.plugin_root_route', 'translations');
        //$package = $parameters['package'] ?? '';

        return "{$baseRoute}/vendors";
    }

}
