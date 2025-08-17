# Modified Files During Customization Setup

## Files Modified During Setup

### 1. Provider Registration

**Purpose:** Register the CustomizationServiceProvider
**Change:** Added custom service provider to the application

⚠️ **IMPORTANT:** Laravel applications use different provider registration patterns. Choose the method that matches your application structure.

**📖 See:** `README-Laravel-Patterns.md` for detailed instructions on identifying and using the correct pattern for your application.

#### Pattern A: Provider Array (Laravel 10 and below, packages like SocietyPal)

**File:** `bootstrap/providers.php`

**Before:**

```php
<?php

return [
    App\Providers\AppServiceProvider::class,
    // ... existing providers
];
```

**After:**

```php
<?php

return [
    App\Providers\AppServiceProvider::class,
    // ... existing providers
    App\Providers\CustomizationServiceProvider::class,
];
```

**Example files:**

-   `before/providers-array-pattern.php` - Before modification
-   `after/providers-array-pattern.php` - After modification
-   `*.template` files - Generic templates with placeholders

#### Pattern B: Application Builder (Laravel 11+)

**File:** `bootstrap/app.php`

**Before:**

```php
->withProviders([
    // Framework Service Providers
    Illuminate\Auth\AuthServiceProvider::class,
    // ... other providers

    // Application Service Providers
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
    App\Providers\EventServiceProvider::class,
    App\Providers\RouteServiceProvider::class,
])
```

**After:**

```php
->withProviders([
    // Framework Service Providers
    Illuminate\Auth\AuthServiceProvider::class,
    // ... other providers

    // Application Service Providers
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
    App\Providers\EventServiceProvider::class,
    App\Providers\RouteServiceProvider::class,

    // Custom Service Providers
    App\Providers\CustomizationServiceProvider::class,
])
```

**Example files:**

-   `before/providers.php` - Before modification (Laravel 11+ style)
-   `after/providers.php` - After modification (Laravel 11+ style)

### 2. package.json

**Purpose:** Add custom asset build scripts and dependencies
**Changes:**

-   Added custom build scripts for SASS/JS compilation
-   Added Laravel Mix and Webpack dependencies
-   Added SASS compilation support
-   Added watch commands for development

**📖 See:** `../4-Package-Scripts/README-Package-Modifications.md` for detailed build system documentation.

**Before:**

```json
{
    "scripts": {
        "dev": "vite",
        "build": "vite build"
    }
}
```

**After:**

```json
{
    "scripts": {
        "dev": "vite",
        "build": "vite build",
        "custom:dev": "webpack --config webpack.custom.cjs --mode development --watch",
        "custom:build": "webpack --config webpack.custom.cjs --mode production",
        "custom:hot": "webpack serve --config webpack.custom.cjs --mode development --hot"
    }
}
```

### 3. .gitignore (if modified)

**Purpose:** Exclude custom build artifacts while tracking source
**Additions:**

```
# Custom build outputs
/public/custom/css/
/public/custom/js/
/public/custom/mix-manifest.json

# But keep custom source files
!/resources/Custom/
!/app/Custom/

# Investment tracking
.investment-tracking/temp/
```

---

## Summary of Modifications

| File                      | Type  | Purpose                  | Risk Level |
| ------------------------- | ----- | ------------------------ | ---------- |
| `bootstrap/providers.php` | Core  | Register custom provider | Low        |
| `package.json`            | Build | Add custom scripts       | Low        |
| `.gitignore`              | VCS   | Manage custom files      | Very Low   |

---

## Modification Guidelines

### ✅ Safe Modifications

-   **bootstrap/providers.php**: Adding service providers
-   **package.json**: Adding custom scripts
-   **composer.json**: Adding custom autoload paths
-   **.env files**: Adding custom environment variables

### ⚠️ Careful Modifications

-   **config/ files**: Only if absolutely necessary
-   **routes/ files**: Prefer custom route files
-   **webpack.mix.js**: Use separate webpack.custom.cjs instead

### ❌ Avoid Modifying

-   **vendor/ files**: Never modify directly
-   **core Laravel files**: Use service providers instead
-   **package vendor files**: Use custom alternatives

---

## Rollback Procedures

### Quick Rollback

```bash
# Restore from git
git checkout HEAD -- bootstrap/providers.php package.json

# Rebuild assets
npm install
npm run build
```

### Full Rollback

```bash
# Remove custom provider registration
sed -i '/CustomizationServiceProvider/d' bootstrap/providers.php

# Remove custom scripts
npm pkg delete scripts.custom:dev scripts.custom:build scripts.custom:hot

# Clear custom files
rm -rf app/Custom/ resources/Custom/ public/custom/
```

---

## Validation Steps

After modifications, verify:

1. **Provider Registration**

    ```bash
    php artisan config:clear
    php artisan config:cache
    # Should not show errors
    ```

2. **Custom Scripts**

    ```bash
    npm run custom:build
    # Should create files in public/custom/
    ```

3. **Application Boot**
    ```bash
    php artisan serve
    # Should start without errors
    ```

---

## Next Steps

After successful modifications:

1. Test the custom service provider loading
2. Create your first custom component
3. Verify custom asset compilation
4. Document any additional modifications needed
