<?php
/*
 * @package     :   Sepremex ManagerFile
 * @handler     :   Master Router
 * @developer   :   Ruben Mc
 * @contact     :   webcorm@gmail.com
 * @company     :   Sepremex | fteditor.php
 * @date        :   6/5/2025 | 21:37
*/

return [
    // Landing page
    'home_page'=> [
        'translation_files_for_lang' => 'Translation files for :lang language',
    ],

    // Navigation
    'navigation' => [
        'label' => 'Translations',
        'group' => 'System',
    ],

    // Page titles
    'page_titles' => [
        'manage_languages' => 'Manage Languages',
        'edit_language' => 'Edit :language Translations',
        'edit_file' => 'Edit :file',
    ],

    // Labels
    'labels' => [
        'language' => 'Language',
        'key' => 'Key',
        'value' => 'Value',
        'new_key' => 'New Key',
        'new_value' => 'New Value',
        'search' => 'Search keys or values...',
        'file' => 'File',
    ],

    // Actions
    'actions' => [
        'add' => 'Add',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'save' => 'Save Changes',
        'back' => 'Back to :target',
        'add_key' => 'Add Translation Key',
    ],

    // Messages
    'messages' => [
        'installed_title' => 'Installed Languages ',
        'installed_languages' => 'All language folders and JSON files detected in the configured lang path.',
        'nested_key_help' => 'Use <code>&lt;~&gt;</code> for nested keys: <code>parent&lt;~&gt;child</code>',
        'no_languages_found' => 'No Languages Found',
        'no_languages_found_desc' => 'No translation files detected in the configured directory.',
    ],

    // Notifications
    'notifications' => [
        'saved' => 'Translations saved successfully!',
        'added' => 'New translation key added successfully!',
        'error' => 'Error: :message',
        'key_exists' => 'This key already exists.',
    ],
];
