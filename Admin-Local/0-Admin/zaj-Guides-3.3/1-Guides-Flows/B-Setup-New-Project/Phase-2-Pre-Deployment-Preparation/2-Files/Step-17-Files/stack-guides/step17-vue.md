# Step 17 Customization: Vue Stack

## Context

-   Vue.js detected in package.json
-   Module system: ESM or CommonJS (detected)
-   Build tool: Vite or Mix

## Actions

-   Create protected directories: `app/Custom/`, `resources/Custom/`, etc.
-   Deploy Vue templates/components from customization system
-   Update build tool config for custom Vue assets
-   Install required npm packages (vue, vue-loader, etc.)
-   Fix PostCSS config for module system compatibility
-   Run asset build and verification scripts

## Scripts to Run

-   `setup-customization.sh`
-   `install-dependencies.sh`
-   `fix-postcss-config.sh`
-   `build-custom-assets.sh`
-   `verify-custom-assets.sh`
-   `verify-service-provider.sh`

## Review Checklist

-   All required npm packages installed
-   PostCSS config matches module system
-   Custom Vue assets build successfully
-   Customization layer is active and vendor-safe
