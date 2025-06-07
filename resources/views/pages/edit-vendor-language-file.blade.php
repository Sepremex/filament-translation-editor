<?php
/*
 * @package     :   Sepremex ManagerFile
 * @handler     :   Master Router
 * @developer   :   Ruben Mc
 * @contact     :   webcorm@gmail.com
 * @company     :   Sepremex | edit-vendor-language-file.blade.php
 * @date        :   6/6/2025 | 22:39
*/
?>
<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold">🍺 Editing: {{ $filename }}.php</h2>
                    <p class="text-sm text-gray-600">{{ $package }} → {{ strtoupper($language) }}</p>
                </div>

                <x-filament::button
                    color="gray"
                    tag="a"
                    href="{{ \Sepremex\FilamentTranslationEditor\Pages\EditVendorLanguage::getUrl(['record' => $package, 'language' => $language]) }}"
                >
                    ← Back to {{ $language }} Files
                </x-filament::button>
            </div>
        </x-slot>

        <div class="mb-4 p-4 bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <span class="text-orange-600 dark:text-orange-400">📦</span>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-orange-800 dark:text-orange-200">
                        <strong>Vendor Package:</strong> {{ $package }} |
                        <strong>Language:</strong> {{ $language }} |
                        <strong>File:</strong> {{ $filename }}.php
                    </p>
                </div>
            </div>
        </div>

        @if(empty($translations))
            <div class="text-center py-12 bg-gray-50 dark:bg-gray-800 rounded-xl border-2 border-dashed border-gray-200 dark:border-gray-700">
                <div class="text-6xl mb-4">🍺</div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">File Empty or Not Found</h3>
                <p class="text-gray-500 dark:text-gray-400">No translations found in {{ $filename }}.php</p>
            </div>
        @else
            <form wire:submit.prevent="save">
                <div class="space-y-4">
                    @foreach($translations as $key => $value)
                        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                            <div class="grid grid-cols-12 gap-4 items-start">
                                <div class="col-span-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Key</label>
                                    <div class="font-mono text-sm p-2 bg-gray-100 rounded border">
                                        {{ $key }}
                                    </div>
                                </div>

                                <div class="col-span-8">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Value</label>
                                    <textarea
                                        wire:model.defer="translations.{{ $key }}"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm resize-none"
                                        rows="1"
                                        onInput="this.style.height = 'auto'; this.style.height = this.scrollHeight + 'px'"
                                    >{{ $value }}</textarea>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    <x-filament::button type="submit" color="primary">
                        🍺 Save Changes
                    </x-filament::button>
                </div>
            </form>
        @endif
    </x-filament::section>
</x-filament-panels::page>
