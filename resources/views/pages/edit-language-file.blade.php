<?php
/*
 * @package     :   Sepremex ManagerFile
 * @handler     :   Master Router
 * @developer   :   Ruben Mc
 * @contact     :   webcorm@gmail.com
 * @company     :   Sepremex | edit-language-file.blade.php
 * @date        :   6/4/2025 | 22:03
*/

// {{ \Sepremex\FilamentTranslationEditor\Pages\EditLanguage::getUrl(['record' => $lang]) }}

?>
<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center justify-between">
                <x-filament::button
                    color="gray"
                    tag="a"
                    href="{{ \Sepremex\FilamentTranslationEditor\Pages\EditLanguage::getUrl(['record' => $lang]) }}"
                >
                    ← Back to Language
                </x-filament::button>
                <x-filament::input.wrapper>
                    <x-slot name="prefix">
                        Search for:
                    </x-slot>
                    <x-filament::input
                        type="text"
                        wire:model.live="search"
                        placeholder="Search keys or values..."
                        class="w-full"
                    />
                    <x-slot name="suffix">
                        {{ count($this->filteredTranslations) }} keys
                        @if($this->search) (filtered from {{ count($this->translations) }}) @endif
                    </x-slot>
                </x-filament::input.wrapper>
                <x-filament::input.wrapper>
                    <x-filament::input.select wire:model.live="perPage">
                        <option value="10">10 per page</option>
                        <option value="20">20 per page</option>
                        <option value="50">50 per page</option>
                        <option value="90">90 per page</option>
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>
        </x-slot>

        <x-filament::section>
            <x-slot name="heading">

                <x-filament::input.wrapper>
                    <x-slot name="prefix">
                        Adding new translation key:
                    </x-slot>
                    <x-filament::input
                        wire:model.live="newKey"
                        placeholder="e.g., validation.required or new_key"
                    />
                    <x-slot name="suffix">
                        Use <code>&lt;~&gt;</code> for nested keys: <code>parent&lt;~&gt;child</code><br/>
                    </x-slot>
                </x-filament::input.wrapper>
            </x-slot>
            <x-slot name="description">
                <div class="flex gap-4 w-full ">
                    <div class="flex-1">
                        <textarea
                            wire:model.live="newValue"
                            placeholder="Translation value"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm resize-none"
                            rows="1"
                        ></textarea>
                    </div>
                    <div class="flex-shrink-0">
                        <x-filament::button
                            wire:click.prevent="addKey"
                            icon="heroicon-o-plus"
                            size="sm"
                            color="success"
                            :disabled="empty($newKey) || empty($newValue)"
                        >
                            Add
                        </x-filament::button>
                    </div>
                </div>
                @if($errors->has('newKey'))
                    <div class="text-red-600 text-xs mt-1">{{ $errors->first('newKey') }}</div>
                @endif
              {{--  @if(session()->has('message'))
                    <div class="mb-4 p-3 bg-green-100 border border-green-300 text-green-700 rounded-lg">
                        {{ session('message') }}
                    </div>
                @endif--}}
            </x-slot>

            <form wire:submit.prevent="save">
                <div class="space-y-4">
                    @foreach ($this->paginatedTranslations as $uuid => $item)

                        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                            <div class="grid grid-cols-12 gap-4 items-start">
                                <div class="col-span-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Key</label>

                                    <x-filament::input
                                        wire:model.defer="translationKeys.{{ $loop->index }}"
                                        value="{{ $item['key'] }}"
                                        class="font-mono text-sm"
                                        readonly
                                    />
                                    @if(str_contains($item['key'], '<~>'))
                                        <div class="text-xs text-gray-500 mt-1">
                                            Nested: {{ str_replace('<~>', ' → ', $item['key']) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="col-span-7">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Value</label>
                                    <textarea
                                        wire:model.defer="translations.{{ $uuid }}.value"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm"
                                        rows="1"
                                    >{{ $item['value'] }}</textarea>
                                </div>

                                <div class="col-span-1">
                                    <x-filament::button
                                        color="danger"
                                        wire:click.prevent="removeKey('{{ $uuid }}')"
                                        icon="heroicon-o-trash"
                                        size="sm"
                                    />
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($this->totalPages > 1)
                    <div class="flex justify-between items-center mt-6">
                        <x-filament::button
                            wire:click="previousPage"
                            :disabled="$currentPage <= 1"
                            color="gray"
                        >
                            ← Previous
                        </x-filament::button>

                        <span class="text-sm text-gray-600">
                        Page {{ $currentPage }} of {{ $this->totalPages }}
                    </span>

                        <x-filament::button
                            wire:click="nextPage"
                            :disabled="$currentPage >= $this->totalPages"
                            color="gray"
                        >
                            Next →
                        </x-filament::button>
                    </div>
                @endif
                <div>
                    <x-filament::button type="submit" color="primary" class="mt-6">
                        Save Changes
                    </x-filament::button>

                </div>


            </form>

        </x-filament::section>
    </x-filament::section>
</x-filament-panels::page>


