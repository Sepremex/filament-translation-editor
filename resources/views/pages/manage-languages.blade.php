<?php
/*
 * @package     :   Sepremex ManagerFile
 * @handler     :   Master Router
 * @developer   :   Ruben Mc
 * @contact     :   webcorm@gmail.com
 * @company     :   Sepremex | manage-languages.blade.php
 * @date        :   6/5/2025 | 14:17
*/

// {{ \Sepremex\FilamentTranslationEditor\Pages\EditLanguage::getUrl(['record' => $lang]) }}
?>
<x-filament-panels::page>
    <x-filament::section
        collapsible="true">
        @php
            $colors = [
                'en' => 'from-blue-500 to-blue-700 text-white',
                'es' => 'from-red-500 to-red-700 text-white',
                'fr' => 'from-indigo-500 to-indigo-700 text-white',
                'de' => 'from-gray-600 to-gray-800 text-white',
                'it' => 'from-green-500 to-green-700 text-white',
            ];
// {{ \Sepremex\FilamentTranslationEditor\Pages\ManageVendorLanguages::getUrl(['package' => $package['name']]) }}

        @endphp


        <x-slot name="heading">
            <div class="flex gap-2">
                <div class="flex-grow-1"> {{ __('filament-translation-editor::fteditor.messages.installed_title') }}</div>
                <div class="flex-grow-0">
                    <!-- AGREGAR ESTE BOTÓN -->
                    @if(config('filament-translation-editor.read_vendor', false))
                        <x-filament::button
                            color="warning"
                            class="sm"
                            tag="a"
                            href="{{ \Sepremex\FilamentTranslationEditor\Pages\ManageVendorPackages::getSlug() }}"
                            icon="heroicon-o-cube"
                        >Vendor Packages
                        </x-filament::button>
                    @endif
                </div>
            </div>

        </x-slot>
        <x-slot name="description">{{ __('filament-translation-editor::fteditor.messages.installed_languages') }}</x-slot>

            <!-- Diseño adaptado a tu array simple -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse ($languages as $lang)
                @php($colorClass = $colors[$lang] ?? 'from-purple-500 to-purple-700 text-white')
                <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow p-6">
                    <!-- Language Header -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <!-- Language Icon stupid classes nunca funcionan... grrrr! -->
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br {{ $colorClass }} flex items-center justify-center">
                                <span class="text-sm font-bold uppercase">{{ $lang }}</span>
                            </div>

                            <!-- Language Info -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ strtoupper($lang) }}</h3>
                            </div>
                        </div>

                        <!-- Status Badge -->
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                            {{ $this->getLanguageDisplayName($lang) }}
                        </span>
                    </div>

                    <!-- Quick Info -->
                    <div class="mb-4 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {!! __('filament-translation-editor::fteditor.home_page.translation_files_for_lang', ['lang' => '<strong>' . $lang . '</strong>']) !!}
                        </p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex space-x-2 gap-1 justify-between">
                        <x-filament::icon-button
                            icon="heroicon-m-pencil-square"
                            color="primary"
                            size="sm"
                            tag="a"
                            href="{{ \Sepremex\FilamentTranslationEditor\Pages\EditLanguage::getUrl(['record' => $lang]) }}"
                            label="{{ __('filament-translation-editor::fteditor.actions.edit') }}"
                            tooltip="{{ __('filament-translation-editor::fteditor.actions.edit') }}"
                        />
                        {{--<x-filament::icon-button
                            icon="heroicon-m-trash"
                            color="danger"
                            size="sm"
                            wire:click="deleteLanguage('{{ $lang }}')"
                            wire:confirm="Are you sure you want to delete this language?"
                            label="{{ __('filament-translation-editor::fteditor.actions.delete') }}"
                            tooltip="{{ __('filament-translation-editor::fteditor.actions.delete') }}"
                        />--}}

                    </div>
                </div>
            @empty
                <!-- Empty State -->
                <div class="col-span-full text-center py-12 bg-gray-50 dark:bg-gray-800 rounded-xl border-2 border-dashed border-gray-200 dark:border-gray-700">
                    <x-heroicon-o-language class="w-12 h-12 text-gray-400 mx-auto mb-4" />
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">{{ __('filament-translation-editor::fteditor.messages.no_languages_found') }}</h3>
                    <p class="text-gray-500 dark:text-gray-400">{{ __('filament-translation-editor::fteditor.messages.no_languages_found_desc') }}</p>
                </div>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-panels::page>

