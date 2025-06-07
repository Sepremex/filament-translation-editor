<?php
/*
 * @package     :   Sepremex ManagerFile
 * @handler     :   Master Router
 * @developer   :   Ruben Mc
 * @contact     :   webcorm@gmail.com
 * @company     :   Sepremex | ManageVendorLanguages.php
 * @date        :   6/6/2025 | 17:55
*/

namespace Sepremex\FilamentTranslationEditor\Pages;

use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Model;

class ManageVendorLanguages extends Page
{
    protected static string $view = 'filament-translation-editor::pages.manage-vendor-languages';

    public $package;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public function mount(?string $package = null): void
    {
        $this->package = $package ?? request()->route('package');

    }

    public function getTitle(): string
    {
        return "Vendor: {$this->package}";
    }


    public static function getSlug(): string
    {
        $baseRoute = config('filament-translation-editor.plugin_root_route', 'translations');
        $package = $parameters['package'] ?? '';
        return "{$baseRoute}/vendor/{$package}";
    }
}
