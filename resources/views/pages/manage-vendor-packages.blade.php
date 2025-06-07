<?php
/*
 * @package     :   Sepremex ManagerFile
 * @handler     :   Master Router
 * @developer   :   Ruben Mc
 * @contact     :   webcorm@gmail.com
 * @company     :   Sepremex | manage-vendor-packages.blade.php
 * @date        :   6/6/2025 | 18:00
*/
?>
<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold">Vendor Packages</h2>
                    <p class="text-sm text-gray-600">Translation packages from vendors</p>
                </div>

                <x-filament::button
                    color="gray"
                    tag="a"
                    href="{{ \Sepremex\FilamentTranslationEditor\Pages\ManageVendorPackages::getSlug() }}"
                >
                    ← Back to Core Languages
                </x-filament::button>
            </div>
        </x-slot>

        @php
            $languageManager = app(\Sepremex\FilamentTranslationEditor\Services\LanguageManager::class);
            $packages = $languageManager->getVendorPackages();
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($packages as $package)
                <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-purple-600 flex items-center justify-center">
                                <span class="text-white text-sm font-bold">📦</span>
                            </div>

                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $package['name'] }}</h3>
                                <p class="text-xs text-gray-500">{{ $package['languages_count'] }} languages</p>
                            </div>
                        </div>

                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                            Vendor
                        </span>
                    </div>

                    <div class="mb-4 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Package: <strong>{{ $package['name'] }}</strong>
                        </p>
                    </div>

                    <x-filament::button
                        color="primary"
                        size="sm"
                        tag="a"
                        href="/{{ filament()->getCurrentPanel()->getPath() }}/{{ config('filament-translation-editor.plugin_root_route', 'translations') }}/vendor/{{ $package['name'] }}"
                        class="w-full"
                    >
                        View Languages
                    </x-filament::button>
                </div>
            @empty
                <div class="col-span-full text-center py-12 bg-gray-50 dark:bg-gray-800 rounded-xl border-2 border-dashed border-gray-200 dark:border-gray-700">
                    <div class="text-6xl mb-4">📦</div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No Vendor Packages Found</h3>
                    <p class="text-gray-500 dark:text-gray-400">No vendor translation packages detected.</p>
                </div>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-panels::page>
