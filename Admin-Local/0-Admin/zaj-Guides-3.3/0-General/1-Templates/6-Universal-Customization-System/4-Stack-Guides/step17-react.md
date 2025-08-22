# Step 17 Customization: React Stack

## Context

-   React detected in package.json
-   Module system: ESM or CommonJS (detected)
-   Build tool: Vite or Mix

## Actions

-   Create protected directories: `app/Custom/`, `resources/Custom/`, etc.
-   Deploy React templates/components from customization system
-   Update build tool config for custom React assets
-   Install required npm packages (react, react-dom, etc.)
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
-   Custom React assets build successfully
-   Customization layer is active and vendor-safe
