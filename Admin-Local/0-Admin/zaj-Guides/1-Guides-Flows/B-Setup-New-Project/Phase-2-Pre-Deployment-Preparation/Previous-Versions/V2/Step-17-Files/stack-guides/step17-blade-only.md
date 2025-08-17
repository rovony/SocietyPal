# Step 17 Customization: Blade-Only Stack

## Context

-   No JavaScript framework detected
-   No npm dependencies required (unless for asset compilation)
-   Module system: CommonJS or ESM (detected)
-   Build tool: Mix, Vite, or none

## Actions

-   Create protected directories: `app/Custom/`, `resources/Custom/`, etc.
-   Deploy only Blade and PHP templates from customization system
-   Skip JS/SCSS templates unless needed for custom assets
-   Update build tool config only if custom assets are present
-   Run verification scripts for PHP/Blade only

## Scripts to Run

-   `setup-customization.sh`
-   `verify-installation.sh`
-   `verify-service-provider.sh`

## Review Checklist

-   No unnecessary npm packages
-   No JS/SCSS files unless explicitly needed
-   Customization layer is active and vendor-safe
