<?php
/*
 * @package     :   Sepremex ManagerFile
 * @handler     :   Master Router
 * @developer   :   Ruben Mc
 * @contact     :   webcorm@gmail.com
 * @company     :   Sepremex | manage-vendor-languages.blade.php
 * @date        :   6/6/2025 | 18:02
*/
?>
<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold">{{ ucfirst($package) }} Languages</h2>
                    <p class="text-sm text-gray-600">Translation files for vendor package</p>
                </div>

                <x-filament::button
                    color="gray"
                    tag="a"
                    href="{{ \Sepremex\FilamentTranslationEditor\Pages\ManageVendorPackages::getUrl() }}"
                >
                    ← Back to Vendor Packages
                </x-filament::button>
            </div>
        </x-slot>

        @php
            $languageManager = app(\Sepremex\FilamentTranslationEditor\Services\LanguageManager::class);

            $languages = $languageManager->getVendorLanguages($package);
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse ($languages as $lang)
                <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-gray-800 dark:bg-gray-200 flex items-center justify-center">
                                <span class="text-white dark:text-gray-800 text-sm font-bold uppercase">{{ $lang['code'] }}</span>
                            </div>

                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ strtoupper($lang['code']) }}</h3>
                                <p class="text-xs text-gray-500">{{ $lang['name'] }}</p>
                            </div>
                        </div>

                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">
                            Vendor
                        </span>
                    </div>

                    <div class="mb-4 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ $lang['php_files_count'] }} PHP files
                        </p>
                        <p class="text-xs text-gray-500">Package: {{ $package }}</p>
                    </div>

                    <x-filament::button
                        color="primary"
                        size="sm"
                        tag="a"
                        href="{{ \Sepremex\FilamentTranslationEditor\Pages\EditVendorLanguageFile::getUrl(['package' => $package, 'language' => $lang['code'], 'filename' => $file]) }}"
                        class="w-full"
                    >
                        Edit Files
                    </x-filament::button>
                </div>
            @empty
                <div class="col-span-full text-center py-12 bg-gray-50 dark:bg-gray-800 rounded-xl border-2 border-dashed border-gray-200 dark:border-gray-700">
                    <div class="text-6xl mb-4">🌍</div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No Languages Found</h3>
                    <p class="text-gray-500 dark:text-gray-400">No translation files found for {{ $package }} package.</p>
                </div>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-panels::page>
