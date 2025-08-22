#!/bin/bash
# UNIVERSAL TEMPLATE SELECTOR
# Analyzes your Laravel project and recommends the best templates

set -e

echo "🎯 Universal Laravel Template Selector"
echo "====================================="

# ============================================================================
# PROJECT ANALYSIS
# ============================================================================

echo "🔍 Analyzing your Laravel project..."

# Basic project detection
LARAVEL_VERSION=$(composer show laravel/framework --no-interaction 2>/dev/null | grep "versions" | head -1 | awk '{print $3}' || echo "unknown")
HAS_FRONTEND=$([ -f "package.json" ] && echo "true" || echo "false")
HAS_VITE=$([ -f "vite.config.js" ] || [ -f "vite.config.mjs" ] && echo "true" || echo "false")
HAS_MIX=$([ -f "webpack.mix.js" ] && echo "true" || echo "false")
HAS_SEEDERS=$([ -f "database/seeders/DatabaseSeeder.php" ] && echo "true" || echo "false")
HAS_FAKER=$(grep -r "Faker\|faker" database/ app/ 2>/dev/null >/dev/null && echo "true" || echo "false")
HAS_MODULES=$([ -d "Modules" ] && echo "true" || echo "false")
HAS_TELESCOPE=$([ -f "config/telescope.php" ] && echo "true" || echo "false")
HAS_DEBUGBAR=$([ -f "config/debugbar.php" ] && echo "true" || echo "false")

echo ""
echo "📊 Project Analysis Results:"
echo "  Laravel Version: $LARAVEL_VERSION"
echo "  Has Frontend: $HAS_FRONTEND"
echo "  Asset Bundler: $([ "$HAS_VITE" = "true" ] && echo "Vite" || [ "$HAS_MIX" = "true" ] && echo "Mix" || echo "None")"
echo "  Has Seeders: $HAS_SEEDERS"
echo "  Uses Faker: $HAS_FAKER"
echo "  Has Modules: $HAS_MODULES"
echo "  Has Telescope: $HAS_TELESCOPE"
echo "  Has Debugbar: $HAS_DEBUGBAR"

# ============================================================================
# TEMPLATE RECOMMENDATIONS
# ============================================================================

echo ""
echo "🎯 Template Recommendations:"
echo "=========================="

# Determine app type
if [ "$HAS_FRONTEND" = "false" ]; then
    APP_TYPE="api-only"
    echo "📡 Detected: API-Only Laravel Application"
elif [ "$HAS_FAKER" = "true" ] || [ "$HAS_SEEDERS" = "true" ]; then
    if [ "$HAS_MODULES" = "true" ]; then
        APP_TYPE="saas-installer"
        echo "🏢 Detected: SaaS Application with Installer"
    else
        APP_TYPE="full-stack-with-seeders"
        echo "🌱 Detected: Full-Stack Application with Seeders"
    fi
elif [ "$HAS_TELESCOPE" = "true" ] || [ "$HAS_DEBUGBAR" = "true" ]; then
    APP_TYPE="staging-debug"
    echo "🔧 Detected: Staging/Debug Environment"
else
    APP_TYPE="standard-fullstack"
    echo "🎯 Detected: Standard Full-Stack Application"
fi

# ============================================================================
# RECOMMENDED BUILD PIPELINE
# ============================================================================

echo ""
echo "🏗️ Recommended Build Pipeline:"
echo "============================="

case $APP_TYPE in
    "api-only")
        echo "📡 API-Only Build Pipeline:"
        echo "  1. composer install --no-dev --optimize-autoloader"
        echo "  2. php artisan config:cache"
        echo "  3. php artisan route:cache"
        echo "  4. Skip asset compilation (no frontend)"
        echo ""
        echo "💡 Template: templates/api-only-build.sh"
        RECOMMENDED_TEMPLATE="api-only-build.sh"
        ;;
    "saas-installer")
        echo "🏢 SaaS Installer Build Pipeline:"
        echo "  1. composer install (include dev - faker needed)"
        echo "  2. npm ci && npm run build"
        echo "  3. php artisan config:cache"
        echo "  4. Keep seeders and factories"
        echo ""
        echo "💡 Template: templates/saas-installer-build.sh"
        RECOMMENDED_TEMPLATE="saas-installer-build.sh"
        ;;
    "full-stack-with-seeders")
        echo "🌱 Full-Stack with Seeders Build Pipeline:"
        echo "  1. composer install (include dev - faker needed)"
        echo "  2. npm ci && npm run build"
        echo "  3. php artisan config:cache"
        echo "  4. Keep seeders for demo data"
        echo ""
        echo "💡 Template: templates/fullstack-seeders-build.sh"
        RECOMMENDED_TEMPLATE="fullstack-seeders-build.sh"
        ;;
    "staging-debug")
        echo "🔧 Staging/Debug Build Pipeline:"
        echo "  1. composer install (include dev - debug tools)"
        echo "  2. npm ci && npm run build"
        echo "  3. php artisan config:cache"
        echo "  4. Enable debugging tools"
        echo ""
        echo "💡 Template: templates/staging-debug-build.sh"
        RECOMMENDED_TEMPLATE="staging-debug-build.sh"
        ;;
    *)
        echo "🎯 Standard Full-Stack Build Pipeline:"
        echo "  1. composer install --no-dev --optimize-autoloader"
        echo "  2. npm ci && npm run build"
        echo "  3. php artisan config:cache"
        echo "  4. php artisan route:cache"
        echo ""
        echo "💡 Template: templates/standard-fullstack-build.sh"
        RECOMMENDED_TEMPLATE="standard-fullstack-build.sh"
        ;;
esac

# ============================================================================
# RECOMMENDED SSH PIPELINE
# ============================================================================

echo ""
echo "🔐 Recommended SSH Pipeline:"
echo "=========================="

if [ "$HAS_MODULES" = "true" ]; then
    echo "🏢 CodeCanyon/SaaS SSH Pipeline:"
    echo "  Phase A: Pre-deployment (backup, maintenance)"
    echo "  Phase B: Post-upload (symlinks, modules, permissions)"
    echo "  Phase C: Post-release (public access, health checks)"
    echo ""
    echo "💡 Template: Use current SSH pipeline (already optimized for CodeCanyon)"
    RECOMMENDED_SSH="codecanyon-ssh-pipeline"
else
    echo "🎯 Standard Laravel SSH Pipeline:"
    echo "  Phase A: Pre-deployment (backup, maintenance)"
    echo "  Phase B: Post-upload (symlinks, permissions)"
    echo "  Phase C: Post-release (public access, optimization)"
    echo ""
    echo "💡 Template: templates/standard-ssh-pipeline/"
    RECOMMENDED_SSH="standard-ssh-pipeline"
fi

# ============================================================================
# GENERATE PROJECT-SPECIFIC CONFIG
# ============================================================================

echo ""
echo "⚙️ Generating project-specific configuration..."

cat > project-analysis.json << EOF
{
  "project_type": "$APP_TYPE",
  "laravel_version": "$LARAVEL_VERSION",
  "has_frontend": $HAS_FRONTEND,
  "asset_bundler": "$([ "$HAS_VITE" = "true" ] && echo "vite" || [ "$HAS_MIX" = "true" ] && echo "mix" || echo "none")",
  "has_seeders": $HAS_SEEDERS,
  "uses_faker": $HAS_FAKER,
  "has_modules": $HAS_MODULES,
  "has_telescope": $HAS_TELESCOPE,
  "has_debugbar": $HAS_DEBUGBAR,
  "recommended_build_template": "$RECOMMENDED_TEMPLATE",
  "recommended_ssh_pipeline": "$RECOMMENDED_SSH",
  "needs_dev_dependencies": $([ "$HAS_FAKER" = "true" ] || [ "$HAS_TELESCOPE" = "true" ] || [ "$HAS_DEBUGBAR" = "true" ] && echo "true" || echo "false")
}
EOF

echo "✅ Project analysis saved to: project-analysis.json"

# ============================================================================
# NEXT STEPS
# ============================================================================

echo ""
echo "🎯 Recommended Next Steps:"
echo "========================"
echo ""
echo "1. 🧪 Test your project locally:"
echo "   cd ../4-Local-Testing-With-Act"
echo "   ./test-scenarios.sh"
echo ""
echo "2. 📋 Copy recommended templates:"
echo "   cp templates/$RECOMMENDED_TEMPLATE your-project/"
echo ""
echo "3. 🚀 Run dry-run test:"
echo "   act workflow_dispatch -W $WORKFLOW_PATH --input app-type=$APP_TYPE"
echo ""
echo "4. 🔧 If issues found, fix them and re-test"
echo ""
echo "5. 🎉 Deploy with confidence!"
echo ""

echo "💡 Pro Tip: Always run the dry-run test before deploying to production!"
echo "💡 Pro Tip: The GitHub Actions workflow can be part of your actual CI/CD pipeline!"
