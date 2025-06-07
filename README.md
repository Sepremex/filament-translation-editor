# Filament Translation Editor

🔤 A real translation manager for Laravel 12 + Filament 3 — zero migrations, zero excuses.

---

## 🧩 Core Features

- ✅ Manage **core** translation files (`/lang/{locale}`, `/lang/{locale}.json`)
- ✅ Supports **package translations** in `/lang/vendor/{package}/{locale}`
- ✅ JSON support (core only — packages use PHP only)
- ✅ Nested arrays using custom `<~>` notation (e.g., `validation<~>between<~>string`)
- ✅ Real-time search & pagination
- ✅ Auto-save mode (optional)
- ✅ 80+ languages with native names
- ✅ No database, no models, no crying

---

## 🙅 What it doesn’t do (yet)

Just to set expectations:

- ❌ Does not create new language files
- ❌ Does not delete files
- ❌ Does not sync missing keys (you forgot them, not me), I have another package for that.
- ❌ Does not generate Excel reports, pay taxes, or make you coffee

---

## 🚀 Installation

```bash
composer require sepremex/filament-translation-editor
```

Add it to your Filament panel:

```php
->plugin(\Sepremex\FilamentTranslationEditor\FilamentTranslationEditorPlugin::make())
```

---

## 🛠 Publishing Resources

### Everything
```bash
php artisan vendor:publish --provider="Sepremex\FilamentTranslationEditor\FilamentTranslationEditorServiceProvider"
```

### Config only
```bash
php artisan vendor:publish --tag=filament-translation-editor-config
```

### Views
```bash
php artisan vendor:publish --tag=filament-translation-editor-views
```

### Translations
```bash
php artisan vendor:publish --tag=filament-translation-editor-translations
```

---

## ⚙️ Config Overview

Available at: `config/filament-translation-editor.php`

```php
'supported_extensions' => ['php', 'json'],
'support_json' => true,
'path' => 'lang',
'auto_save' => false,
'key_separator' => '<~>', // not in use from config yet...
'per_page' => 20, // secret for next stage...
'search_enabled' => true,
'allow_delete' => true,
'include_vendor_languages' => true,
'vendor_namespace' => 'vendor',
'default_locale' => 'en',
```

---

## ❤️ Disclaimer

> I wanted it clean and elegant...  
> But Filament had other plans.  
> So here we are — custom pages, magical readers, and zero database migrations.  
> You're welcome.

Use it. Abuse it. Translate responsibly.  
Built with sarcasm and ❤️ by [Sepremex].

### Thanks for trying this out

---

## 📄 License

MIT — porque lo bueno se comparte, excepto la tóxica.
