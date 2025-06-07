<?php
/*
 * @package     :   Sepremex ManagerFile
 * @handler     :   Master Router
 * @developer   :   Ruben Mc
 * @contact     :   webcorm@gmail.com
 * @company     :   Sepremex | edit-language.blade.php
 * @date        :   6/5/2025 | 14:34
*/
?>
<x-filament-panels::page>
    <x-slot name="header">
        <h1 class="text-xl font-bold tracking-tight">
            Editing Language: {{ $language }}
        </h1>
    </x-slot>

    <div class="space-y-8">

        <x-filament::tabs label="Language Settings" wire:model.live="activeTab" class="-mb-2">
            <x-filament::tabs.item
               icon="heroicon-m-document-text"
               badge="{{ count($phpFiles) }}"
               :active="$activeTab === 'php'"
               wire:click="$set('activeTab', 'php')"
            >PHP
            </x-filament::tabs.item>

            <x-filament::tabs.item
               icon="heroicon-m-code-bracket"
               badge="1"
               :active="$activeTab === 'json'"
               wire:click="$set('activeTab', 'json')"
            >JSON
            </x-filament::tabs.item>

        </x-filament::tabs>
        <div class="mt-2">
            <div wire:loading wire:target="activeTab" class="text-center py-4">
                <x-filament::loading-indicator class="h-5 w-5 mx-auto" />
            </div>

            <!-- Contenido -->
            @if($activeTab === 'php')
                <x-filament::section>
                    <x-slot name="heading">PHP Files</x-slot>

                    @if (count($phpFiles))
                        <div class="flex justify-start gap-2">
                            @foreach ($phpFiles as $file)
                                <x-filament::button color="gray" tag="a" size="sm"
                                                    href="{{ \Sepremex\FilamentTranslationEditor\Pages\EditLanguageFile::getUrl(['lang' => $language,'file' => pathinfo($file, PATHINFO_FILENAME),]) }}">
                                    {{ $file }}
                                </x-filament::button>
                            @endforeach
                        </div>

                    @else
                        <p class="text-sm text-gray-500">No PHP translation files found.</p>
                    @endif
                </x-filament::section>
            @elseif($activeTab === 'json')
                <x-filament::section>
                    <x-slot name="heading">JSON File</x-slot>

                    @if ($hasJson)
                        <div class="flex justify-start gap-2">
                            <x-filament::button color="gray" tag="a" size="sm"
                                                href="{{ \Sepremex\FilamentTranslationEditor\Pages\EditLanguageFile::getUrl(['lang' => $language,'file' => '__json',]) }}">
                                {{ $language }}.json
                            </x-filament::button>
                        </div>
                    @else
                        <p class="text-sm text-gray-500">No JSON translation file found.</p>
                    @endif
                </x-filament::section>

            @endif
        </div>



    </div>
</x-filament-panels::page>

