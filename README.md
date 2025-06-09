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
- ✅ **Auto-sync keys across languages** — add once, sync everywhere
- ✅ **Smart array cleanup** — removes empty parent arrays automatically
- ✅ 80+ languages with native names
- ✅ No database, no models, no crying

---

## 🙅 What it doesn't do (yet)

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

## 🎯 Best Practices (Or: How Not to Break Things)

### 🔍 Search Before You Add

**Golden Rule**: Always search for a key before adding it.

```
❌ Don't: Add `user.name` when `user` already exists as a string
✅ Do: Search "user" first, then decide wisely
```

The plugin will auto-sync your new keys across all languages, but it won't fix your poor life choices.

### 🧠 Nested Arrays 101

Use `<~>` for nested structures:

```
✅ Good: validation<~>email<~>required → 'validation' => ['email' => ['required' => 'value']]
❌ Bad: validation.email.required → 'validation.email.required' => 'value'
```

Dots (`.`) are **literal** — they're part of the key, not separators. Don't be that person.

### ⚡ Auto-Sync Magic

When you add/remove keys, they automatically sync to other languages:

- **Adding**: Creates the key in all languages (if it doesn't exist)
- **Removing**: Removes from all languages + cleans empty parent arrays
- **Existing keys**: Won't overwrite — we're smart like that

### 🚨 Common Gotchas

1. **Mixed Types**: Don't mix strings and arrays for the same key across languages
2. **Empty Search**: Use the search box — it's there for a reason
3. **Backup First**: Enable `create_backup` in config if you're feeling dangerous
4. **Case Sensitivity**: `User` ≠ `user` — Laravel cares, so should you

---

## 🔧 Pro Workflow

1. **🔍 Search** for existing keys first
2. **📝 Add** only what doesn't exist
3. **💾 Save** (or enable auto-save for YOLO mode)
4. **🎉 Enjoy** your magically synced translations

Remember: The plugin is smarter than your average developer, but it can't fix stupidity.

---

## ❤️ Disclaimer

> I wanted it clean and elegant...  
> But Filament had other plans.  
> So here we are — custom pages, magical readers, zero database migrations, and auto-sync wizardry.  
> You're welcome.

Use it. Abuse it. Translate responsibly.  
Built with sarcasm, auto-sync magic, and ❤️ by [Sepremex].

### Thanks for trying this out

---

## 📄 License

MIT — porque lo bueno se comparte, excepto la tóxica.
