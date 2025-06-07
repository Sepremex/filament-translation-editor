<?php
/*
 * @package     :   Sepremex ManagerFile
 * @handler     :   Master Router
 * @developer   :   Ruben Mc
 * @contact     :   webcorm@gmail.com
 * @company     :   Sepremex | EditLanguageFile.php
 * @date        :   6/5/2025 | 14:37
*/
namespace Sepremex\FilamentTranslationEditor\Pages;

use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Sepremex\FilamentTranslationEditor\Services\LanguageFileReader;
use Sepremex\FilamentTranslationEditor\Services\LanguageFileWriter;


class EditLanguageFile extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';
    protected static string $view = 'filament-translation-editor::pages.edit-language-file';

    public string $lang;
    public string $file;
    public array $translations = [];
    public string $newKey = '';
    public string $newValue = '';
    public bool $isJson = false;
    public string $search = '';
    public int $perPage = 20;
    public int $currentPage = 1;

    public function getTitle(): string
    {
        return "Edit: {$this->file} [{$this->lang}]";
    }

    public static function getSlug(): string
    {
        return 'translation-editor/{lang}/edit/{file}';
    }

    public static function shouldRegisterNavigation(): bool
    {
        // Leave it... don't touch it!
        return false;
    }

    public function mount(string $lang, string $file): void
    {
        $this->lang = $lang;
        $this->file = $file;
        $this->isJson = $file === '__json';
        $reader = app(LanguageFileReader::class);
        $this->translations = $this->convertToUuidStructure($reader->read($lang, $file));
    }

    public function save(): void
    {
        $writer = app(LanguageFileWriter::class);

        $flatArray = [];
        foreach ($this->translations as $item) {
            $flatArray[$item['key']] = $item['value'];
        }

         if($writer->write($this->lang, $this->file, $flatArray)) {
            Notification::make()
                ->title('Saving translations')
                ->body('All done!')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Error saving translations')
                ->body('Can\'t write translations!')
                ->danger()
                ->send();
        }
    }

    protected function convertToUuidStructure(array $flattened): array
    {
        //$flattened = ArrayHelper::flatten($translations);
        $result = [];

        foreach ($flattened as $key => $value) {
            $uuid = 'uuid-' . uniqid();
            $result[$uuid] = [
                'key' => $key,
                'value' => $value
            ];
        }

        return $result;
    }

    public function addKey(): void
    {
        if (empty($this->newKey) || empty($this->newValue)) {
            return;
        }

        // Verificar duplicados
        foreach ($this->translations as $item) {
            if ($item['key'] === $this->newKey) {
                $this->addError('newKey', 'This key already exists.');
                return;
            }
        }

        // Crear nueva traducción
        $uuid = 'uuid-' . uniqid();
        $newItem = [
            'key' => $this->newKey,
            'value' => $this->newValue
        ];

        // INSERTAR AL PRINCIPIO en lugar del final
        $this->translations = [$uuid => $newItem] + $this->translations;

        // Limpiar campos
        $newkyadded = $this->newKey;
        $this->newKey = '';
        $this->newValue = '';
        $this->resetErrorBag();

        // Ir a la primera página para mostrar el elemento nuevo
        $this->currentPage = 1;

        // Limpiar búsqueda para asegurar que se vea
        $this->search = '';


        $this->checkAutosave();

        // Notificación visual
        //session()->flash('message', 'New translation key added! Remember to save your changes.');
        Notification::make()
            ->title('Translation added!')
            ->body("Key '{$newkyadded}' was added to the top of the list. Don't forget to save your changes.")
            ->success()
            ->duration(5000)
            ->send();
    }

    public function removeKey(string $uuid): void
    {
        $oldskey = $this->translations[$uuid];
        unset($this->translations[$uuid]);

        // Si después de eliminar no hay elementos filtrados, limpiar búsqueda
        if (!empty($this->search) && empty($this->filteredTranslations)) {
            $this->search = '';
            $this->currentPage = 1;
            unset($this->filteredTranslations);
            unset($this->paginatedTranslations);
            $this->dispatch('$refresh');
        }

        // Si estamos en una página que ya no tiene elementos, ir a la anterior
        if (empty($this->paginatedTranslations) && $this->currentPage > 1) {
            $this->currentPage--;
        }

        $this->checkAutosave();

        Notification::make()
            ->title('Translation removed!')
            ->body("Key '{$oldskey['key']}' has been removed. Don't forget to save your changes.")
            ->success()
            ->duration(5000)
            ->send();


    }


    public function getFilteredTranslationsProperty(): array
    {
        if (empty($this->search)) {
            return $this->translations;
        }

        return array_filter($this->translations, function($item, $uuid) {
            if (!isset($item['key']) || !isset($item['value'])) {
                return false;
            }

            $searchTerm = strtolower($this->search);

            // Buscar en la key
            $keyMatch = str_contains(strtolower($item['key']), $searchTerm);

            // Buscar en el value
            $valueMatch = str_contains(strtolower($item['value']), $searchTerm);

            return $keyMatch || $valueMatch;
        }, ARRAY_FILTER_USE_BOTH);
    }

    public function getPaginatedTranslationsProperty(): array
    {
        // Usar los datos YA FILTRADOS
        $filtered = $this->filteredTranslations; // ← Cambio importante
        $offset = ($this->currentPage - 1) * $this->perPage;

        return array_slice($filtered, $offset, $this->perPage, true);
    }
    public function getTotalPagesProperty(): int
    {
        return ceil(count($this->filteredTranslations) / $this->perPage); // ← Usar filtrados
    }

    // Métodos de paginación
    public function nextPage(): void
    {
        if ($this->currentPage < $this->getTotalPagesProperty()) {
            $this->currentPage++;
        }
    }

    public function previousPage(): void
    {
        if ($this->currentPage > 1) {
            $this->currentPage--;
        }
    }

    public function updatedSearch(): void
    {
        $this->currentPage = 1;
    }

    public function updatedPerPage(): void
    {
        $this->currentPage = 1;
    }

    private function checkAutosave(): void
    {
        $autoSiNo = config('filament-translation-editor.auto_save_changes', false);
        if ($autoSiNo) {
            $this->save();
        }
    }


}
