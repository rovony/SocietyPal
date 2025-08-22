# Step 17 Customization: Inertia Stack

## Context

-   Inertia.js detected in package.json
-   JS framework: Vue or React (detected)
-   Module system: ESM or CommonJS (detected)
-   Build tool: Vite or Mix

## Actions

-   Create protected directories: `app/Custom/`, `resources/Custom/`, etc.
-   Deploy Inertia templates/components from customization system
-   Update build tool config for custom Inertia assets
-   Install required npm packages (inertia, inertia adapters, etc.)
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
-   Custom Inertia assets build successfully
-   Customization layer is active and vendor-safe
