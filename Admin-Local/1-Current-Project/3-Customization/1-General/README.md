# Project Customization System Guide

**Generated:** Sat Aug 16 12:43:13 EDT 2025  
**Workflow:** step17  
**Context:** initial-setup  

## Your Project Configuration

**JavaScript Framework:** blade-only  
**Module System:** esm  
**Build Tool:** vite  

## What This System Provides

### 🛡️ **Update-Safe Customization Layer**
Your project now has a three-layer protection system that ensures all customizations survive vendor updates:

1. **Vendor Layer (Protected)** - Original marketplace files that should never be edited
2. **Custom Layer (Safe)** - Your customizations in `app/Custom/` and `resources/Custom/`
3. **Integration Layer (Bridge)** - Service provider that connects everything

### 📁 **Your Custom Directories**
```
app/Custom/
├── Controllers/          # Custom controllers
├── Models/              # Custom models  
├── Services/            # Business logic
├── Helpers/             # Utility classes
├── config/              # Custom configuration
└── ...

resources/Custom/
├── views/               # Blade templates
├── css/                 # SCSS files
├── js/                  # JavaScript files
└── images/              # Custom images

public/Custom/           # Compiled assets
├── css/                 # Compiled CSS
├── js/                  # Compiled JS
└── images/              # Optimized images
```


## Blade-Only Project

Your project uses traditional Laravel Blade templates without a JavaScript framework.

### Custom Blade Templates
Create Blade templates in `resources/Custom/views/`:
```blade
{{-- resources/Custom/views/dashboard/custom.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="custom-dashboard">
    <!-- Your custom content -->
</div>
@endsection

@customCss('dashboard.css')
@customJs('dashboard.js')
```

### Asset Management
```bash
# Build custom assets
npm run custom:build

# Development watching
npm run custom:dev
```


## How to Customize

### 1. **Controllers**
Create custom controllers in `app/Custom/Controllers/`:
```php
<?php
namespace App\Custom\Controllers;

use App\Http\Controllers\Controller;

class CustomDashboardController extends Controller
{
    public function index()
    {
        return view('custom::dashboard.index');
    }
}
```

### 2. **Routes**
Add custom routes in `app/Custom/routes/web.php`:
```php
<?php
use App\Custom\Controllers\CustomDashboardController;

Route::get('/custom-dashboard', [CustomDashboardController::class, 'index'])
    ->name('custom.dashboard');
```

### 3. **Configuration**
Use custom config in `app/Custom/config/custom-app.php`:
```php
<?php
return [
    'features' => [
        'dashboard' => env('CUSTOM_DASHBOARD_ENABLED', true),
        'analytics' => env('CUSTOM_ANALYTICS_ENABLED', false),
    ],
    'branding' => [
        'company_name' => env('CUSTOM_COMPANY_NAME', 'Your Company'),
        'theme_color' => env('CUSTOM_THEME_COLOR', '#007bff'),
    ],
];
```

### 4. **Feature Toggles**
Use in views:
```blade
@ifCustomFeature('dashboard')
    @include('custom::dashboard.widgets')
@endifCustomFeature
```

Use in controllers:
```php
if (config('custom.features.dashboard')) {
    // Custom dashboard logic
}
```

## Update Safety

### ✅ **Your Customizations Are Protected**
- All custom files are in protected directories
- Vendor updates cannot overwrite your work
- Service provider automatically reconnects everything after updates

### ✅ **After Vendor Updates**
```bash
# 1. Clear caches
php artisan config:clear && php artisan route:clear

# 2. Rebuild custom assets  
npm run custom:build

# 3. Verify everything works
php artisan list | grep -i custom
```

## Quick Commands

```bash
# Check system status
php artisan list | grep -i custom

# Rebuild assets
npm run custom:build

# Development mode
npm run custom:dev

# Verify installation
bash Admin-Local/0-Admin/zaj-Guides/0-General/1-Templates/6-Universal-Customization-System/2-Scripts/1-Setup/4-Verification/verify-installation.sh
```

---

**💡 Remember:** Always create new files in the `Custom/` directories. Never edit vendor files directly!

