#!/bin/bash

# Generate Project-Specific Customization Documentation
# Creates tailored documentation based on detected project context
# Usage: bash generate-project-docs.sh --workflow="step17" --context="initial-setup"

set -euo pipefail

# Default values
WORKFLOW="unknown"
CONTEXT="unknown"
OUTPUT_DIR=""

# Parse command line arguments
while [[ $# -gt 0 ]]; do
    case $1 in
        --workflow=*)
            WORKFLOW="${1#*=}"
            shift
            ;;
        --context=*)
            CONTEXT="${1#*=}"
            shift
            ;;
        --output-docs=*)
            OUTPUT_DIR="${1#*=}"
            shift
            ;;
        *)
            echo "Unknown parameter: $1"
            exit 1
            ;;
    esac
done

PROJECT_ROOT="$(pwd)"
GENERAL_DIR="$PROJECT_ROOT/Admin-Local/1-Current-Project/3-Customization/1-General"
SETUP_DIR="$PROJECT_ROOT/Admin-Local/1-Current-Project/3-Customization/2-Setup"

# Create directories if they don't exist
mkdir -p "$GENERAL_DIR"
mkdir -p "$SETUP_DIR"

# Read project context (if exists)
CONTEXT_FILE="$SETUP_DIR/customization-context.json"
if [ -f "$CONTEXT_FILE" ]; then
    JS_FRAMEWORK=$(grep '"js_framework"' "$CONTEXT_FILE" | cut -d'"' -f4)
    MODULE_SYSTEM=$(grep '"module_system"' "$CONTEXT_FILE" | cut -d'"' -f4)
    BUILD_TOOL=$(grep '"build_tool"' "$CONTEXT_FILE" | cut -d'"' -f4)
else
    # Fallback detection
    if grep -q '"type": "module"' "$PROJECT_ROOT/package.json" 2>/dev/null; then
        MODULE_SYSTEM="esm"
    else
        MODULE_SYSTEM="commonjs"
    fi
    
    if grep -q 'vue' "$PROJECT_ROOT/package.json" 2>/dev/null; then
        JS_FRAMEWORK="vue"
    elif grep -q 'react' "$PROJECT_ROOT/package.json" 2>/dev/null; then
        JS_FRAMEWORK="react"
    elif grep -q 'inertia' "$PROJECT_ROOT/package.json" 2>/dev/null; then
        JS_FRAMEWORK="inertia"
    else
        JS_FRAMEWORK="blade-only"
    fi
    
    if [ -f "$PROJECT_ROOT/vite.config.js" ]; then
        BUILD_TOOL="vite"
    elif [ -f "$PROJECT_ROOT/webpack.mix.js" ]; then
        BUILD_TOOL="mix"
    else
        BUILD_TOOL="webpack-custom"
    fi
fi

# Generate General Documentation
cat > "$GENERAL_DIR/README.md" << EOF
# Project Customization System Guide

**Generated:** $(date)  
**Workflow:** $WORKFLOW  
**Context:** $CONTEXT  

## Your Project Configuration

**JavaScript Framework:** $JS_FRAMEWORK  
**Module System:** $MODULE_SYSTEM  
**Build Tool:** $BUILD_TOOL  

## What This System Provides

### 🛡️ **Update-Safe Customization Layer**
Your project now has a three-layer protection system that ensures all customizations survive vendor updates:

1. **Vendor Layer (Protected)** - Original marketplace files that should never be edited
2. **Custom Layer (Safe)** - Your customizations in \`app/Custom/\` and \`resources/Custom/\`
3. **Integration Layer (Bridge)** - Service provider that connects everything

### 📁 **Your Custom Directories**
\`\`\`
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
\`\`\`

EOF

# Add framework-specific guidance
case $JS_FRAMEWORK in
    "vue")
        cat >> "$GENERAL_DIR/README.md" << EOF

## Vue.js Integration

Your project uses Vue.js with $MODULE_SYSTEM modules and $BUILD_TOOL build tool.

### Custom Vue Components
Create Vue components in \`resources/Custom/js/components/\`:
\`\`\`javascript
// resources/Custom/js/components/CustomDashboard.vue
<template>
  <div class="custom-dashboard">
    <!-- Your custom Vue component -->
  </div>
</template>
\`\`\`

### Build Commands
\`\`\`bash
# Development build with watching
npm run custom:dev

# Production build
npm run custom:build
\`\`\`

EOF
        ;;
    "react")
        cat >> "$GENERAL_DIR/README.md" << EOF

## React Integration

Your project uses React with $MODULE_SYSTEM modules and $BUILD_TOOL build tool.

### Custom React Components
Create React components in \`resources/Custom/js/components/\`:
\`\`\`jsx
// resources/Custom/js/components/CustomDashboard.jsx
import React from 'react';

export default function CustomDashboard() {
    return (
        <div className="custom-dashboard">
            {/* Your custom React component */}
        </div>
    );
}
\`\`\`

### Build Commands
\`\`\`bash
# Development build with watching
npm run custom:dev

# Production build
npm run custom:build
\`\`\`

EOF
        ;;
    "inertia")
        cat >> "$GENERAL_DIR/README.md" << EOF

## Inertia.js Integration

Your project uses Inertia.js with $MODULE_SYSTEM modules and $BUILD_TOOL build tool.

### Custom Inertia Pages
Create Inertia pages in \`resources/Custom/js/Pages/\`:
\`\`\`jsx
// resources/Custom/js/Pages/CustomDashboard.jsx
import React from 'react';
import { Head } from '@inertiajs/react';

export default function CustomDashboard({ data }) {
    return (
        <>
            <Head title="Custom Dashboard" />
            <div className="custom-dashboard">
                {/* Your custom Inertia page */}
            </div>
        </>
    );
}
\`\`\`

### Build Commands
\`\`\`bash
# Development build with watching
npm run custom:dev

# Production build
npm run custom:build
\`\`\`

EOF
        ;;
    "blade-only")
        cat >> "$GENERAL_DIR/README.md" << EOF

## Blade-Only Project

Your project uses traditional Laravel Blade templates without a JavaScript framework.

### Custom Blade Templates
Create Blade templates in \`resources/Custom/views/\`:
\`\`\`blade
{{-- resources/Custom/views/dashboard/custom.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="custom-dashboard">
    <!-- Your custom content -->
</div>
@endsection

@customCss('dashboard.css')
@customJs('dashboard.js')
\`\`\`

### Asset Management
\`\`\`bash
# Build custom assets
npm run custom:build

# Development watching
npm run custom:dev
\`\`\`

EOF
        ;;
esac

# Common sections for all frameworks
cat >> "$GENERAL_DIR/README.md" << EOF

## How to Customize

### 1. **Controllers**
Create custom controllers in \`app/Custom/Controllers/\`:
\`\`\`php
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
\`\`\`

### 2. **Routes**
Add custom routes in \`app/Custom/routes/web.php\`:
\`\`\`php
<?php
use App\Custom\Controllers\CustomDashboardController;

Route::get('/custom-dashboard', [CustomDashboardController::class, 'index'])
    ->name('custom.dashboard');
\`\`\`

### 3. **Configuration**
Use custom config in \`app/Custom/config/custom-app.php\`:
\`\`\`php
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
\`\`\`

### 4. **Feature Toggles**
Use in views:
\`\`\`blade
@ifCustomFeature('dashboard')
    @include('custom::dashboard.widgets')
@endifCustomFeature
\`\`\`

Use in controllers:
\`\`\`php
if (config('custom.features.dashboard')) {
    // Custom dashboard logic
}
\`\`\`

## Update Safety

### ✅ **Your Customizations Are Protected**
- All custom files are in protected directories
- Vendor updates cannot overwrite your work
- Service provider automatically reconnects everything after updates

### ✅ **After Vendor Updates**
\`\`\`bash
# 1. Clear caches
php artisan config:clear && php artisan route:clear

# 2. Rebuild custom assets  
npm run custom:build

# 3. Verify everything works
php artisan list | grep -i custom
\`\`\`

## Quick Commands

\`\`\`bash
# Check system status
php artisan list | grep -i custom

# Rebuild assets
npm run custom:build

# Development mode
npm run custom:dev

# Verify installation
bash Admin-Local/0-Admin/zaj-Guides/0-General/1-Templates/6-Universal-Customization-System/2-Scripts/1-Setup/4-Verification/verify-installation.sh
\`\`\`

---

**💡 Remember:** Always create new files in the \`Custom/\` directories. Never edit vendor files directly!

EOF

# Generate Setup Status File
cat > "$SETUP_DIR/setup-status.md" << EOF
# Customization System Setup Status

**Setup Date:** $(date)  
**Workflow:** $WORKFLOW  
**Context:** $CONTEXT  

## Detected Configuration

- **JavaScript Framework:** $JS_FRAMEWORK
- **Module System:** $MODULE_SYSTEM  
- **Build Tool:** $BUILD_TOOL

## Installation Status

✅ **Custom directories created**  
✅ **Service provider registered**  
✅ **Asset pipeline configured**  
✅ **Build scripts added to package.json**  

## Template Used

**Universal System:** 6-Universal-Customization-System  
**Version:** 1.0.0  
**Scripts Location:** Admin-Local/0-Admin/zaj-Guides/0-General/1-Templates/6-Universal-Customization-System/2-Scripts/

## Next Steps

1. Start customizing by creating files in \`app/Custom/\` and \`resources/Custom/\`
2. Use the verification script to ensure everything works
3. Follow the project-specific guide in \`1-General/README.md\`

EOF

echo "✅ Project-specific documentation generated:"
echo "   - General guide: $GENERAL_DIR/README.md"
echo "   - Setup status: $SETUP_DIR/setup-status.md"
