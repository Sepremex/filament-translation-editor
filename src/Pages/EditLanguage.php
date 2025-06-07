<?php
/*
 * @package     :   Sepremex ManagerFile
 * @handler     :   Master Router
 * @developer   :   Ruben Mc
 * @contact     :   webcorm@gmail.com
 * @company     :   Sepremex | EditLanguage.php
 * @date        :   6/5/2025 | 14:33
*/

namespace Sepremex\FilamentTranslationEditor\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Str;
use Sepremex\FilamentTranslationEditor\Services\LanguageManager;

class EditLanguage extends Page
{
    protected static string $view = 'filament-translation-editor::pages.edit-language';

    protected static ?string $navigationIcon = null;
    protected static bool $shouldRegisterNavigation = false;

    public string $language;
    public array $phpFiles = [];
    public bool $hasJson = false;

    public string $record;
    public string $activeTab = 'php';

    public function mount(string $record): void
    {
        $this->language = $record;
        $manager = app(LanguageManager::class);
        $this->phpFiles = $manager->getPhpFiles($record);
        $this->hasJson = $manager->hasJsonFile($record);
    }
    public static function getSlug(): string
    {
        return 'translation-editor/{record}'; // aquí 'record' = idioma
    }
    public function getTitle(): string
    {
        return config('filament-translation-editor.navigation.page_lang_title', 'Manage Languages') . ': ' . $this->language;
    }

}
