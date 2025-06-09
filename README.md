# Filament Translation Editor

🔤 A real translation manager for Laravel 12 + Filament 3 — zero migrations, zero excuses.

---

## 🔧 Auto-Translation Setup

### 🆓 **LibreTranslate (Recommended for Development)**

**Local Docker (Free):**
```bash
docker run -d -p 5000:5000 --name libretranslate libretranslate/libretranslate:latest
```

**Configuration:**
```env
FILAMENT_TRANSLATION_AUTO_TRANSLATE=true
FILAMENT_TRANSLATION_PROVIDER=libretranslate
LIBRETRANSLATE_URL=http://localhost:5000
# API key optional for most instances
LIBRETRANSLATE_API_KEY=
```

### 💰 **Microsoft Translator (2M chars/month free)**

1. Create Azure account
2. Create Translator resource
3. Get API key and region

```env
FILAMENT_TRANSLATION_PROVIDER=microsoft
MICROSOFT_TRANSLATOR_KEY=your_key_here
MICROSOFT_TRANSLATOR_REGION=eastus
```

### 💸 **Google Translate (Paid, no free tier)**

1. Create Google Cloud account
2. Enable Translation API
3. Get API key

```env
FILAMENT_TRANSLATION_PROVIDER=google
GOOGLE_TRANSLATE_KEY=your_api_key_here
```

### ⚠️ **Translation Provider Warnings:**

- **LibreTranslate**: May be slower, quality varies by language pair
- **Microsoft**: Free tier has monthly limits, then charges apply
- **Google**: No free tier, charges from first character
- **All providers**: May fail due to network issues, quotas, or API changes

---# Filament Translation Editor

🔤 A real translation manager for Laravel 12 + Filament 3 — zero migrations, zero excuses.

> ⚠️ **EARLY RELEASE WARNING**: This is an early version under active development. While extensively tested in development environments, **use with caution in production**. Always backup your translation files before use. See [Production Usage](#-production-usage-warning) below.

---

## 🚨 Production Usage Warning

### ⚠️ **IMPORTANT DISCLAIMER**

This plugin is in **early development** and, while functional, may contain bugs or edge cases not yet discovered. **I am not responsible for any data loss, corruption, or issues** that may arise from using this plugin.

### 🛡️ **Before Using in Production:**

1. **BACKUP EVERYTHING** — Your entire `/lang/` directory and any vendor translation files
2. **Test thoroughly** in a development environment first
3. **Start small** — Test with non-critical translation files
4. **Monitor logs** — Check `storage/logs/laravel.log` for translation service errors
5. **Have a rollback plan** — Know how to restore from backups quickly

### 🔥 **Known Potential Issues:**

- **API quotas** — Translation providers have limits that may be exceeded
- **Network failures** — External APIs may be unreachable
- **File permissions** — Ensure Laravel can write to language directories
- **Large files** — Very large translation files may timeout
- **Concurrent edits** — Multiple users editing same files simultaneously

### 💡 **Recommended Production Setup:**

```php
// Enable backups (strongly recommended)
'create_backup' => true,

// Disable auto-save for more control
'auto_save_changes' => false,

// Consider disabling auto-translate initially
'auto_translate' => ['enabled' => false],
```

**You have been warned. Use at your own risk.** 🫡

---

## 🧩 Core Features

- ✅ Manage **core** translation files (`/lang/{locale}`, `/lang/{locale}.json`)
- ✅ Supports **package translations** in `/lang/vendor/{package}/{locale}`
- ✅ JSON support (core only — packages use PHP only)
- ✅ Nested arrays using custom `<~>` notation (e.g., `validation<~>between<~>string`)
- ✅ Real-time search & pagination
- ✅ Auto-save mode (optional)
- ✅ **Auto-translate with multiple providers** — LibreTranslate, Microsoft, Google
- ✅ **Auto-sync keys across languages** — add once, sync everywhere
- ✅ **Smart array cleanup** — removes empty parent arrays automatically
- ✅ **Graceful fallback** — if translation fails, returns original text
- ✅ 80+ languages with native names
- ✅ No database, no models, no crying

---

## 🙅 What it doesn't do (yet)

Just to set expectations:

- ❌ Does not create backup language files
- ❌ Does not create new language files
- ❌ Does not delete files
- ❌ Does not sync missing keys (you forgot them, not me), I have another package for that.
- ❌ Does not generate Excel reports, pay taxes, or make you coffee
- ❌ Does not guarantee your translation provider won't hit quotas/limits
- ❌ Does not fix bad life choices (like not searching before adding keys)

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
'path' => 'lang',
'auto_save' => false,
'key_separator' => '<~>', // not in use from config yet...
'per_page' => 20, // secret for next stage...
'search_enabled' => true,
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
3. **Backup First**: Do it manually
4. **Case Sensitivity**: `User` ≠ `user` — Laravel cares, so should you
5. **API Limits**: Translation providers have quotas — monitor your usage
6. **Network Dependencies**: Auto-translate requires internet — have a backup plan

### 🔧 Pro Workflow

1. **🔍 Search** for existing keys first
2. **📝 Add** only what doesn't exist
3. **👀 Review** auto-translations (they're not perfect)
4. **💾 Save** (or enable auto-save for YOLO mode)
5. **📊 Monitor** logs for translation service issues
6. **🎉 Enjoy** your magically synced translations

Remember: The plugin is smarter than your average developer, but it can't fix stupidity or API outages.

---

## ❤️ Disclaimer

> I wanted it clean and elegant...  
> But Filament had other plans.  
> So here we are — custom pages, magical readers, zero database migrations, auto-sync wizardry, and translation APIs that may or may not work when you need them most.  
> You're welcome... and you're warned.

Use it. Abuse it. Translate responsibly. **Backup religiously.**  
Built with sarcasm, auto-sync magic, questionable life choices, and ❤️ by [Sepremex].

### ⚖️ Legal Stuff

This software is provided "as is" without warranty of any kind. The author is not responsible for:
- Lost translations
- Corrupted files
- Exceeded API quotas
- Existential crises caused by bad translations
- Your production going down at 3 AM
- Angry users complaining about "Hello world" being translated to "Goodbye universe"

**Use at your own risk. Backup your stuff. Test before deploying.**

### Thanks for being a brave early adopter

---

## 📄 License

MIT — porque lo bueno se comparte, excepto la responsabilidad legal.
