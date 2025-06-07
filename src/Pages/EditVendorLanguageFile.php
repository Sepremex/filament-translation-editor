<?php
/*
 * @package     :   Sepremex ManagerFile
 * @handler     :   Master Router
 * @developer   :   Ruben Mc
 * @contact     :   webcorm@gmail.com
 * @company     :   Sepremex | EditorVendorLanguagefile.php
 * @date        :   6/6/2025 | 22:34
*/

namespace Sepremex\FilamentTranslationEditor\Pages;

use Filament\Pages\Page;

class EditVendorLanguageFile extends Page
{
    protected static string $view = 'filament-translation-editor::pages.edit-vendor-language-file';

    public $package;
    public $language;
    public $filename;
    public $translations = [];

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function getSlug(): string
    {
        $baseRoute = config('filament-translation-editor.plugin_root_route', 'translations');
        return "{$baseRoute}/vendor/{record}/languages/{language}/files/{filename}";
    }

    public function mount(): void
    {
        $this->package = request()->route()->parameter('record');
        $this->language = request()->route()->parameter('language');
        $this->filename = request()->route()->parameter('filename');

        $this->loadVendorTranslations();
    }

    protected function loadVendorTranslations(): void
    {
        $languageManager = app(\Sepremex\FilamentTranslationEditor\Services\LanguageManager::class);

        try {
            // Cargar traducciones del vendor package
            $this->translations = $languageManager->readVendorTranslationFile(
                $this->package,
                $this->language,
                $this->filename
            );
        } catch (\Exception $e) {
            $this->translations = [];
        }
    }

    public function getTitle(): string
    {
        return "Edit {$this->filename}.php - {$this->language} ({$this->package})";
    }

    public function save(): void
    {
        try {
            $languageManager = app(\Sepremex\FilamentTranslationEditor\Services\LanguageManager::class);

            $success = $languageManager->writeVendorTranslationFile(
                $this->package,
                $this->language,
                $this->filename,
                $this->translations
            );

            if ($success) {
                \Filament\Notifications\Notification::make()
                    ->title('🍺 Vendor translations saved successfully!')
                    ->body("File {$this->filename}.php updated for {$this->package}")
                    ->success()
                    ->send();
            } else {
                \Filament\Notifications\Notification::make()
                    ->title('Failed to save translations')
                    ->danger()
                    ->send();
            }

        } catch (\Exception $e) {
            \Filament\Notifications\Notification::make()
                ->title('Error saving translations')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
