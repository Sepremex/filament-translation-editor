<?php
/*
 * @package     :   Sepremex ManagerFile
 * @handler     :   Master Router
 * @developer   :   Ruben Mc
 * @contact     :   webcorm@gmail.com
 * @company     :   Sepremex | ManageVendorPackage.php
 * @date        :   6/6/2025 | 21:40
*/

namespace Sepremex\FilamentTranslationEditor\Pages;

use Illuminate\Database\Eloquent\Model;
use Filament\Pages\Page;

class ManageVendorPackage extends Page
{
    protected static string $view = 'filament-translation-editor::pages.manage-vendor-languages';

    public string $package;
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public function getTitle(): string
    {
        return 'Vendor Package';
    }

    // Y en mount():
    public function mount($record = null): void
    {
        $this->package = request()->route()->parameter('record');
    }


    public static function getSlug(): string
    {
        $baseRoute = config('filament-translation-editor.plugin_root_route', 'translations');
        return "{$baseRoute}/vendor/{record}";
    }

}
