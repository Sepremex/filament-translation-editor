<?php
/*
 * @package     :   Sepremex ManagerFile
 * @handler     :   Master Router
 * @developer   :   Ruben Mc
 * @contact     :   webcorm@gmail.com
 * @company     :   Sepremex | filament-translation-editor.php
 * @date        :   6/4/2025 | 21:52
*/

/**
 * Disclaimer
 *
 * I wanted this to be clean, elegant, and Laravelish™️...
 * But then Filament happened.
 *
 * It started beautifully with a simple, raw Laravel 12 setup —
 * a model-less structure, virtual files, it was poetry.
 *
 * Then I added it to Filament... and everything went to 💩.
 * Route model binding? Poof.
 * Resource-style pages? Nope.
 * Middleware assumptions? Oh you sweet summer child.
 *
 * So now we’re using custom pages.
 * You’ll survive. I might refactor it one day...
 *
 * Thanks for downloading and giving it a spin. ❤️
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Plugin Routes
    |--------------------------------------------------------------------------
    */
    'plugin_root_route' => env('FILAMENT_TRANSLATION_EDITOR_ROUTE', 'translations'),
    /*
    |--------------------------------------------------------------------------
    | Vendor Support
    |--------------------------------------------------------------------------
    | when false it will ignore read_vendor and vendor_package
    */
    'vendor_auto_detect' => false, // Auto-detect packages in /vendor/
    /*
     | when true
     | reads values from vendor_packages
     |
     */
    'read_vendor' => env('FILAMENT_TRANSLATION_EDITOR_VENDOR', false),
    /*
     | when empty | it reads all vendors
     | otherwise it will try to read vendors with the name
     |
     */
    'vendor_packages' => [
        // 'my-awesome-package', 'her-awesome-package'...etc
    ],

    /*
    |--------------------------------------------------------------------------
    | Show or nah
    |--------------------------------------------------------------------------
    |
    | Do you want toy show the link on the sidebar?
    |
    */
    'show_in_navigation' => true,
    /*
    |--------------------------------------------------------------------------
    | Language Files Path
    |--------------------------------------------------------------------------
    |
    | This value determines the path where your language files are stored.
    | By default, Laravel 11+ uses '/lang' in the root directory.
    | Older versions used '/resources/lang'. You can customize this path
    | according to your application's structure.
    |
    */
    'language_path' => env('FILAMENT_TRANSLATION_EDITOR_LANG_PATH', base_path('lang')),

    /*
    |--------------------------------------------------------------------------
    | Supported File Extensions
    |--------------------------------------------------------------------------
    |
    | This exists because someone (yes, me — the AI you trusted)
    | thought it would be “cool” to support multiple file types.
    | So here it is: PHP arrays and JSON files. Deal with it.
    |
    | Want to add YAML, INI, or ancient Sumerian? Don’t. Not yet.
    |
    */
    'supported_extensions' => ['php', 'json'],

    /*
    |--------------------------------------------------------------------------
    | Supported JSON File
    |--------------------------------------------------------------------------
    |
    | Regardless of the settings above...
    | JSON is special. Sacred. Untouchable.
    |
    | Just leave it as true.
    | It works. Don’t break it.
    | Also, yes — this setting is my fault too. ❤️
    |
    */
    'support_json' => true,

    /*
    |--------------------------------------------------------------------------
    | Excluded Files
    |--------------------------------------------------------------------------
    |
    | Files that should be excluded from the translation editor.
    | These files will not appear in the file list.
    |
    */
    'excluded_files' => [
        // Add any files you want to exclude
        // 'validation.php',
    ],

    /*
    |--------------------------------------------------------------------------
    | Navigation
    |--------------------------------------------------------------------------
    |
    | Configure the navigation settings for the translation editor.
    |
    */
    'navigation' => [
        'group' => 'System',
        'icon' => 'heroicon-o-language',
        'sort' => 10,
        'label' => 'Translations',
        'page_title' => 'Core Translations',
        'page_lang_title'=>'Editing language'
    ],

    /*
    |--------------------------------------------------------------------------
    | Permissions
    |--------------------------------------------------------------------------
    |
    | Define permissions for accessing the translation editor.
    | Set to null to disable permission checks.
    |
    */
    'permissions' => [
        'view_translations' => 'view_translations',
        'edit_translations' => 'edit_translations',
        'delete_translations' => 'delete_translations',
    ],

    /*
    |--------------------------------------------------------------------------
    | Backup
    |--------------------------------------------------------------------------
    |
    | Whether to create backups before modifying translation files.
    |
    */
    'create_backup' => env('FILAMENT_TRANSLATION_EDITOR_BACKUP', true),

    /*
    |--------------------------------------------------------------------------
    | Auto-detect Languages
    |--------------------------------------------------------------------------
    |
    | Whether to automatically detect available languages from the filesystem
    | or require manual configuration.
    |
    */
    'auto_detect_languages' => true,

    /*
    |--------------------------------------------------------------------------
    | Default Language
    |--------------------------------------------------------------------------
    |
    | The default language code for your application.
    |
    */
    'default_language' => env('APP_LOCALE', 'en'),
    /*
    |--------------------------------------------------------------------------
    | Auto save
    |--------------------------------------------------------------------------
    |
    | When adding new key or removing one you can auto save
    | or leave it as false, but you have to click on [Save Changes]
    | to make the changes permanent.
    |
    */
    'auto_save_changes' => true,
];
