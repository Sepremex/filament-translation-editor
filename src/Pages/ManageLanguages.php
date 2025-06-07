<?php

/*
 * @package     :   Sepremex ManagerFile
 * @handler     :   Master Router
 * @developer   :   Ruben Mc
 * @contact     :   webcorm@gmail.com
 * @company     :   Sepremex | ManageLanguages.php
 * @date        :   6/5/2025 | 14:15
*/

namespace Sepremex\FilamentTranslationEditor\Pages;

use Filament\Pages\Page;

use Sepremex\FilamentTranslationEditor\Services\LanguageManager;
use Sepremex\FilamentTranslationEditor\FilamentTranslationEditorPlugin;
use Sepremex\FilamentTranslationEditor\Utils\ArrayHelper;

class ManageLanguages extends Page
{
    protected static string $view = 'filament-translation-editor::pages.manage-languages';

    protected static ?string $navigationIcon = 'heroicon-o-language';
    protected static ?string $title = 'Manage Languages';
   // protected static ?string $slug = 'languages';
    protected static ?int $navigationSort = -1;

    public array $languages = [];
    public static function getSlug(): string
    {
        return config('filament-translation-editor.plugin_root_route', 'translations');
    }

    public function mount(): void
    {
        $this->languages = app(LanguageManager::class)->getAvailableLanguages();
      //  static::$slug = config('filament-translation-editor.plugin_root_route', 'translations');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return config('filament-translation-editor.show_in_navigation', false);
    }

    public static function getNavigationLabel(): string
    {
        return FilamentTranslationEditorPlugin::getNavigationLabel();
    }
    public function getTitle(): string
    {
        return config('filament-translation-editor.navigation.page_title', 'Manage Languages');
    }

    public static function getNavigationGroup(): ?string
    {
        return FilamentTranslationEditorPlugin::getNavigationGroup();
    }

    public static function getNavigationIcon(): ?string
    {
        return FilamentTranslationEditorPlugin::getNavigationIcon();
    }

    public static function getNavigationSort(): ?int
    {
        return FilamentTranslationEditorPlugin::getNavigationSort();
    }

    public function getLanguageDisplayName(string $code): string
    {
        // FIXME... don't ask, please...
        return ArrayHelper::getLanguageName($code);
    }

}
