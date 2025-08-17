# Package.json Build System Enhancements

This document explains how to enhance your Laravel application's build system to support custom asset compilation with SASS, Laravel Mix, and Webpack.

## Overview

The customization system includes frontend assets (SCSS, JavaScript) that need to be compiled separately from the main application assets. This approach ensures:

1. **Update Safety** - Custom assets are compiled independently
2. **Flexibility** - Different build tools for different needs
3. **Performance** - Separate custom asset bundles
4. **Maintainability** - Clear separation of custom vs vendor assets

## Dependencies to Add

Add these dependencies to your `package.json`:

```json
{
    "devDependencies": {
        "laravel-mix": "^6.0.49",
        "sass": "^1.90.0",
        "sass-loader": "^16.0.5",
        "webpack": "^5.101.2",
        "webpack-cli": "^6.0.1"
    }
}
```

**Why each dependency:**

-   `laravel-mix` - Simplified webpack configuration for Laravel
-   `sass` + `sass-loader` - SCSS compilation support
-   `webpack` + `webpack-cli` - Advanced bundling and optimization

## Custom Scripts

Add these scripts to your `package.json`:

```json
{
    "scripts": {
        "custom:dev": "mix --mix-config=webpack.custom.cjs",
        "custom:build": "mix --mix-config=webpack.custom.cjs --production",
        "custom:watch": "mix --mix-config=webpack.custom.cjs --watch",
        "custom:clean": "rm -rf public/Custom/js/* public/Custom/css/*",
        "custom:setup": "npm run custom:clean && npm run custom:build"
    }
}
```

**Script purposes:**

-   `custom:dev` - Development build (unminified, with source maps)
-   `custom:build` - Production build (minified, optimized)
-   `custom:watch` - Development with file watching
-   `custom:clean` - Remove compiled custom assets
-   `custom:setup` - Full setup (clean + production build)

## Usage Examples

```bash
# Development workflow
npm run custom:dev        # One-time development build
npm run custom:watch      # Watch for changes during development

# Production deployment
npm run custom:setup      # Clean setup for production
npm run custom:build      # Production build only

# Maintenance
npm run custom:clean      # Clean compiled assets
```

## Integration with Existing Build

Your existing application build system (Vite, Laravel Mix, etc.) continues to work normally:

```bash
# Main application assets
npm run dev               # Your existing dev command
npm run build             # Your existing build command

# Custom assets (separate)
npm run custom:dev        # Custom development build
npm run custom:build      # Custom production build
```

## File Structure After Build

```
public/
├── Custom/
│   ├── css/
│   │   └── app.css       # Compiled from resources/Custom/css/app.scss
│   └── js/
│       └── app.js        # Compiled from resources/Custom/js/app.js
└── build/                # Your existing Vite/Mix builds
    ├── assets/
    └── manifest.json
```

## Webpack Configuration

The scripts reference `webpack.custom.cjs` which should be created in your project root. This file configures Laravel Mix specifically for custom assets.

See the `3-Build-System/` folder for the complete webpack configuration template.

## Benefits

1. **Independent Compilation** - Custom assets don't interfere with main build
2. **Technology Choice** - Use different build tools for different needs
3. **Update Safety** - Vendor updates won't break custom asset compilation
4. **Performance** - Separate bundles for better caching and loading
5. **Development Experience** - Hot reloading and source maps for custom assets

## Template Files

-   `package-enhanced.json` - Complete example with all dependencies
-   `package-enhanced.json.template` - Template with placeholders for integration
-   `package-modifications.json` - Just the additions to merge with existing package.json
