# Package.json Modifications for Priority Task Analytics Dashboard

## How to Apply These Changes

### MODIFY (Don't Create New) - Update your existing package.json

Add these scripts to your existing `package.json` file in the `scripts` section:

```json
{
    "scripts": {
        // ... your existing scripts ...

        // Analytics-specific build scripts
        "analytics:dev": "npm run development -- --mix-config=webpack.custom.cjs",
        "analytics:watch": "npm run watch -- --mix-config=webpack.custom.cjs --watch-analytics",
        "analytics:hot": "npm run hot -- --mix-config=webpack.custom.cjs",
        "analytics:build": "npm run production -- --mix-config=webpack.custom.cjs --analytics-only",
        "analytics:clean": "rimraf public/Custom/js public/Custom/css public/Custom/mix-manifest.json",
        "analytics:deploy": "npm run analytics:clean && npm run analytics:build",

        // Combined build scripts (main app + analytics)
        "dev:all": "concurrently \"npm run development\" \"npm run analytics:dev\"",
        "watch:all": "concurrently \"npm run watch\" \"npm run analytics:watch\"",
        "build:all": "npm run production && npm run analytics:build"
    }
}
```

Add these dependencies to your existing `package.json`:

```json
{
    "devDependencies": {
        // ... your existing devDependencies ...
        "@types/chart.js": "^2.9.37",
        "chart.js": "^4.4.0",
        "concurrently": "^8.2.0",
        "rimraf": "^5.0.1"
    },

    "dependencies": {
        // ... your existing dependencies ...
        "moment": "^2.29.4"
    }
}
```

Optionally add this custom section for analytics configuration:

```json
{
    "analytics": {
        "version": "1.0.0",
        "build": {
            "source": "resources/Custom",
            "output": "public/Custom",
            "assets": ["js/analytics.js", "css/analytics.css", "js/components/"]
        },
        "dependencies": {
            "external": ["chart.js", "moment"],
            "internal": ["AnalyticsDashboard", "TaskMetrics", "PriorityCharts"]
        }
    }
}
```

## Script Usage

### Development

-   `npm run analytics:dev` - Build analytics assets for development
-   `npm run analytics:watch` - Watch analytics files for changes
-   `npm run analytics:hot` - Hot module replacement for analytics

### Production

-   `npm run analytics:build` - Build optimized analytics assets
-   `npm run analytics:clean` - Clean analytics build files
-   `npm run analytics:deploy` - Clean and build for deployment

### Combined (Main App + Analytics)

-   `npm run dev:all` - Build both main app and analytics
-   `npm run watch:all` - Watch both main app and analytics
-   `npm run build:all` - Production build for both

## Installation Steps

1. Copy the script additions to your `package.json`
2. Run `npm install` to install new dependencies
3. Copy `webpack.custom.cjs` to your project root
4. Create the analytics asset structure in `resources/Custom/`
5. Run `npm run analytics:dev` to test the build

## Benefits

-   **Separate Builds**: Analytics assets compile independently
-   **Cache Busting**: Versioned assets for proper caching
-   **Development Tools**: Hot reloading, source maps, BrowserSync
-   **Production Optimization**: Minification, tree shaking, dead code elimination
-   **Vendor Safety**: Analytics assets won't be overwritten by vendor updates
