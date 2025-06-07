<?php

/*
 * @package     :   Sepremex ManagerFile
 * @handler     :   Master Router
 * @developer   :   Ruben Mc
 * @contact     :   webcorm@gmail.com
 * @company     :   Sepremex | FilamentTranslationEditor.php
 * @date        :   6/4/2025 | 21:17
*/

namespace Sepremex\FilamentTranslationEditor;

use Filament\Contracts\Plugin;
use Filament\Panel;

class FilamentTranslationEditorPlugin implements Plugin
{
    public function getId(): string
    {
        return 'filament-translation-editor';
    }

    public function register(Panel $panel): void
    {
       // $baseRoute = config('filament-translation-editor.plugin_root_route', 'translations');

        $panel->pages([
            \Sepremex\FilamentTranslationEditor\Pages\ManageLanguages::class,
            \Sepremex\FilamentTranslationEditor\Pages\EditLanguage::class,
            \Sepremex\FilamentTranslationEditor\Pages\EditLanguageFile::class,
            \Sepremex\FilamentTranslationEditor\Pages\ManageVendorPackages::class,
            \Sepremex\FilamentTranslationEditor\Pages\ManageVendorPackage::class,
            \Sepremex\FilamentTranslationEditor\Pages\EditVendorLanguage::class,
            \Sepremex\FilamentTranslationEditor\Pages\EditVendorLanguageFile::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        // Optional: nothing here unless needed
    }

    public static function make(): static
    {
        return app(static::class);
    }


    public static function getNavigationLabel(): string
    {
        return config('filament-translation-editor.navigation.label', 'Translations');
    }

    public static function getNavigationGroup(): ?string
    {
        return config('filament-translation-editor.navigation.group', 'System');
    }

    public static function getNavigationIcon(): ?string
    {
        return config('filament-translation-editor.navigation.icon', 'heroicon-o-language');
    }

    public static function getNavigationSort(): ?int
    {
        $sort = config('filament-translation-editor.navigation.sort');
        return $sort !== null ? (int) $sort : null;
    }
}
