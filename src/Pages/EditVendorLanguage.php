<?php
/*
 * @package     :   Sepremex ManagerFile
 * @handler     :   Master Router
 * @developer   :   Ruben Mc
 * @contact     :   webcorm@gmail.com
 * @company     :   Sepremex | EditVendorLanguage.php
 * @date        :   6/6/2025 | 22:24
*/

namespace Sepremex\FilamentTranslationEditor\Pages;

use Filament\Pages\Page;

class EditVendorLanguage extends Page
{
    protected static string $view = 'filament-translation-editor::pages.edit-vendor-language';

    public string $package;
    public string $language;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function getSlug(): string
    {
        $baseRoute = config('filament-translation-editor.plugin_root_route', 'translations');
        return "{$baseRoute}/vendor/{record}/languages/{language}";
    }

    public function mount(): void
    {
        $this->package = request()->route()->parameter('record');
        $this->language = request()->route()->parameter('language');
    }

    public function getTitle(): string
    {
        return "Edit {$this->language} - {$this->package}";
    }

    protected function formatFileSize(int $bytes): string
    {
        if ($bytes === 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes, 1024));

        return round($bytes / pow(1024, $i), 2) . ' ' . $units[$i];
    }
}

