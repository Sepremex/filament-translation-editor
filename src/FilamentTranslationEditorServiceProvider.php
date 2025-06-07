<?php
/*
 * @package     :   Sepremex ManagerFile
 * @handler     :   Master Router
 * @developer   :   Ruben Mc
 * @contact     :   webcorm@gmail.com
 * @company     :   Sepremex | FilamentTranslationEditorServiceProvider.php
 * @date        :   6/4/2025 | 21:53
*/

namespace Sepremex\FilamentTranslationEditor;

use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;

class FilamentTranslationEditorServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/filament-translation-editor.php',
            'filament-translation-editor'
        );
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'filament-translation-editor');
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'filament-translation-editor');
        Filament::serving(function () {
            // You can register assets or hook into Filament here if needed
        });

        $this->publishes([
            __DIR__ . '/../config/filament-translation-editor.php' => config_path('filament-translation-editor.php'),
        ], 'filament-translation-editor-config');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/filament-translation-editor'),
        ], 'filament-translation-editor-views');

        $this->publishes([
            __DIR__ . '/../lang' => $this->app->langPath('vendor/filament-translation-editor'),
        ], 'filament-translation-editor-translations');
    }
}
