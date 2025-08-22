#!/bin/bash

# Data Persistence Script - Prevents User Data Loss During Deployments
# Usage: bash Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-2-Pre-Deployment-Preparation/2-Files/Step-18-Files/link_persistent_dirs.sh "/path/to/release" "/path/to/shared"

RELEASE_PATH="${1:-$(pwd)}"
SHARED_PATH="${2:-$(pwd)/../shared}"

echo "🔗 Setting up data persistence links..."
echo "  Release: $RELEASE_PATH"
echo "  Shared:  $SHARED_PATH"

# Universal exclusion list for public/ directory
PUBLIC_EXCLUSIONS=(
    "css"
    "js"
    "build"
    "_next"
    "static"
    "assets"
    "dist"
    "mix-manifest.json"
    "hot"
    "webpack"
    "vite"
    ".vite"
    "fonts"  # if generated/built
)

# Detect application type for specific exclusions
if [ -f "$RELEASE_PATH/artisan" ]; then
    # Laravel application
    PUBLIC_EXCLUSIONS+=("mix-manifest.json" "hot")
elif [ -f "$RELEASE_PATH/package.json" ]; then
    # Check for specific frameworks
    if grep -q "next" "$RELEASE_PATH/package.json"; then
        PUBLIC_EXCLUSIONS+=("_next" ".next" "out")
    elif grep -q "react\|vue" "$RELEASE_PATH/package.json"; then
        PUBLIC_EXCLUSIONS+=("static" "assets" "manifest.json")
    fi
fi

echo "📋 Public folder sharing strategy:"
echo "   Rule: Share ALL public/ contents EXCEPT build artifacts"
echo "   Excluded: ${PUBLIC_EXCLUSIONS[*]}"

# Ensure shared directories exist
mkdir -p "$SHARED_PATH"/{storage,public}
mkdir -p "$SHARED_PATH/storage"/{app/public,framework/{cache,sessions,views},logs}

# Create shared public structure with smart exclusions
if [ ! -d "$SHARED_PATH/public" ]; then
    mkdir -p "$SHARED_PATH/public"

    # Move existing public contents to shared (first deployment only)
    if [ -d "$RELEASE_PATH/public" ]; then
        echo "📦 Moving existing public contents to shared (first deployment)..."

        for item in "$RELEASE_PATH/public"/*; do
            if [ -e "$item" ]; then
                ITEM_NAME=$(basename "$item")

                # Check if item should be excluded
                SHOULD_EXCLUDE=false
                for exclusion in "${PUBLIC_EXCLUSIONS[@]}"; do
                    if [[ "$ITEM_NAME" == "$exclusion" ]]; then
                        SHOULD_EXCLUDE=true
                        echo "   ⚠️ Excluding from shared: $ITEM_NAME (build artifact)"
                        break
                    fi
                done

                # Move to shared if not excluded
                if [ "$SHOULD_EXCLUDE" = false ]; then
                    mv "$item" "$SHARED_PATH/public/"
                    echo "   ✅ Moved to shared: $ITEM_NAME"
                fi
            fi
        done
    fi
fi

# Create exclusion documentation
cat > "$SHARED_PATH/public/.shared-exclusions" << EXCLUSIONS_EOF
# Public Folder Sharing Strategy - "One-Line Rule"
# Generated: $(date)

# RULE: Share ALL public/ contents EXCEPT build artifacts

# ✅ INCLUDED (shared across deployments):
# - User uploads (uploads/, user-uploads/, media/)
# - Static assets (images/, fonts/ if not built)
# - Configuration files (.htaccess, robots.txt)
# - SSL/Security files (.well-known/)

# ❌ EXCLUDED (rebuilt each deployment):
$(printf "# - %s/\n" "${PUBLIC_EXCLUSIONS[@]}")

# This ensures user-generated content persists while
# allowing each release to have its own optimized assets.
EXCLUSIONS_EOF

# Set permissions
chmod -R 775 "$SHARED_PATH/storage" "$SHARED_PATH/public" 2>/dev/null || true

cd "$RELEASE_PATH"

# Link environment file (production secrets)
if [ -f "$SHARED_PATH/.env" ]; then
    rm -f .env
    ln -nfs "$(realpath --relative-to="$(pwd)" "$SHARED_PATH")"/.env .env
    echo "✅ Environment file linked"
fi

# Link storage directory (Laravel framework + user files)
if [ -d "$SHARED_PATH/storage" ]; then
    rm -rf storage
    ln -nfs "$(realpath --relative-to="$(pwd)" "$SHARED_PATH")/storage" storage
    echo "✅ Storage directory linked"
fi

# Smart public directory linking with exclusions
if [ -d "$SHARED_PATH/public" ]; then
    # Remove public directory and create symlink to shared
    rm -rf public
    ln -nfs "$(realpath --relative-to="$(pwd)" "$SHARED_PATH")/public" public

    # Recreate excluded directories in the release (these will be built)
    echo "🔄 Recreating build artifact directories in release..."
    for exclusion in "${PUBLIC_EXCLUSIONS[@]}"; do
        case "$exclusion" in
            "css"|"js"|"build"|"assets"|"static"|"dist"|"_next")
                mkdir -p "public/$exclusion"
                echo "   ✅ Created build directory: public/$exclusion"
                ;;
            "mix-manifest.json"|"hot"|"manifest.json")
                # These are files, will be created by build process
                ;;
        esac
    done

    echo "✅ Smart public directory linked with exclusions"
fi

# Create Laravel storage link for publicly accessible files
if command -v php >/dev/null && [ -f "artisan" ]; then
    php artisan storage:link 2>/dev/null || echo "ℹ️ Laravel storage:link skipped"
fi

echo "🛡️ Data persistence setup complete"
echo ""
echo "📊 Protected directories:"
echo "  - storage/          # Laravel storage (logs, cache, sessions)"
echo "  - public/ (smart)   # User content preserved, build artifacts excluded"
echo "  - .env              # Environment configuration"
echo ""
echo "🎯 Strategy: Share everything EXCEPT build artifacts"
echo "✅ Result: User data survives ALL deployments"