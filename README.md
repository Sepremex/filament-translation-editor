# Filament Translation Editor

🔤 A full-featured translation manager for Laravel 12 + Filament 3, without database overhead.

---

## 🧩 Features

- ✅ Manage PHP and JSON translation files directly
- ✅ Supports nested array keys with custom `<~>` separator
- ✅ Real-time search & filtering
- ✅ Pagination for large files
- ✅ Livewire-powered tabbed interface
- ✅ Auto-save (configurable)
- ✅ Multi-language support with 80+ native names
- ✅ Fully responsive design
- ✅ Dark/Light mode
- ✅ Plugin translations via `/lang/vendor/fteditor.php`
- ✅ Chainable config methods for power users
- ✅ Publishable config, views, and language files

---

## 🚀 Installation

```bash
composer require sepremex/filament-translation-editor
```

Register the plugin inside your Filament panel:

```php
->plugin(\Sepremex\FilamentTranslationEditor\FilamentTranslationEditorPlugin::make())
```

---

## 🛠 Configuration & Assets

### Publish everything

```bash
php artisan vendor:publish --provider="Sepremex\FilamentTranslationEditor\FilamentTranslationEditorServiceProvider"
```

### Publish config only

```bash
php artisan vendor:publish --tag=filament-translation-editor-config
```

### Publish views

```bash
php artisan vendor:publish --tag=filament-translation-editor-views
```

### Publish translations

```bash
php artisan vendor:publish --tag=filament-translation-editor-translations
```

---

## ⚙️ Config File Overview

Located at: `config/filament-translation-editor.php`

```php
'supported_extensions' => ['php', 'json'],
'support_json' => true,
'path' => 'lang',
'auto_save' => false,
'key_separator' => '<~>',
'per_page' => 20,
'search_enabled' => true,
'allow_delete' => true,
'include_vendor_languages' => false,
'default_locale' => 'en',
```

---

## ❤️ Disclaimer

> I wanted this to be clean, elegant, and Laravelish™️...  
> But then Filament happened.  
> Route model binding? Poof. Resource-style pages? Nope.  
> So now we’re using custom pages. You’ll survive. I might refactor it one day...

Thanks for trying this out — made with 💖 by [Sepremex].

---

## 🧪 Tests

Coming soon!

## 📄 License

MIT — use it, hack it, improve it.
