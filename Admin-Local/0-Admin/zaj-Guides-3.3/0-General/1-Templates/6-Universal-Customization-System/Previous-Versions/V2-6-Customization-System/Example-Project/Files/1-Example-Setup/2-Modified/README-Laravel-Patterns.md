# Laravel Provider Registration Patterns

This guide covers different ways to register the CustomizationServiceProvider based on your Laravel application structure.

## Pattern 1: Provider Array (Laravel 10 and below, many packages)

**File:** `bootstrap/providers.php`

**When to use:**

-   Laravel applications using traditional provider array pattern
-   Package-based Laravel applications (SocietyPal, CodeCanyon scripts, etc.)
-   Older Laravel versions

**Implementation:**

```php
<?php

return [
    // ... existing providers
    App\Providers\CustomizationServiceProvider::class,
];
```

**Example files:**

-   `before/providers-array-pattern.php` - Before adding customization
-   `after/providers-array-pattern.php` - After adding customization
-   `*.template` files - Generic templates with placeholders

## Pattern 2: Application Builder (Laravel 11+)

**File:** `bootstrap/app.php`

**When to use:**

-   Fresh Laravel 11+ applications
-   Applications using the new application builder pattern

**Implementation:**

```php
<?php

use Illuminate\Foundation\Application;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        App\Providers\CustomizationServiceProvider::class,
    ])
    // ... rest of configuration
```

**Example files:**

-   `before/providers.php` - Before adding customization (Laravel 11+ style)
-   `after/providers.php` - After adding customization (Laravel 11+ style)

## How to Identify Your Pattern

1. **Check your Laravel version:**

    ```bash
    php artisan --version
    ```

2. **Check your bootstrap directory:**

    - If you have `bootstrap/providers.php` → Use Pattern 1
    - If you have `bootstrap/app.php` with Application::configure() → Use Pattern 2

3. **Look at existing providers:**
    - Array return statement → Pattern 1
    - ->withProviders() method → Pattern 2

## Build System Enhancement

Both patterns can use the enhanced package.json for custom asset compilation:

**File:** `package.json`

```json
{
    "scripts": {
        "custom:build": "mix --mix-config=webpack.custom.cjs --production",
        "custom:dev": "mix --mix-config=webpack.custom.cjs",
        "custom:watch": "mix --mix-config=webpack.custom.cjs --watch",
        "custom:clean": "rm -rf public/Custom/js/* public/Custom/css/*",
        "custom:setup": "npm run custom:clean && npm run custom:build"
    },
    "devDependencies": {
        "laravel-mix": "^6.0.49",
        "sass": "^1.90.0",
        "sass-loader": "^16.0.5",
        "webpack": "^5.101.2",
        "webpack-cli": "^6.0.1"
    }
}
```

**Dependencies to add:**

-   `laravel-mix` - For custom asset compilation
-   `sass` + `sass-loader` - For SCSS support
-   `webpack` + `webpack-cli` - For advanced bundling

**Custom scripts:**

-   `npm run custom:dev` - Development build with watch
-   `npm run custom:build` - Production build
-   `npm run custom:clean` - Clean compiled assets
-   `npm run custom:setup` - Full setup (clean + build)
