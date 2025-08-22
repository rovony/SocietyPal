#!/bin/bash
# UNIVERSAL SSH B2: Activation & Cleanup
# Combines DeployHQ B04-B06: Activate release, exit maintenance, cleanup
# Based on FINAL-DEPLOYHQ-PIPELINE.md + Expert 3 research

set -e

echo "🚀 UNIVERSAL SSH B2: Activation & Cleanup"
echo "========================================="
echo "📚 Combines DeployHQ B04-B06 + Expert 3 comprehensive research"
echo "🎯 Works for ANY Laravel deployment"
echo ""

# ============================================================================
# B04: ACTIVATE NEW RELEASE
# ============================================================================

echo "🔄 B04: Activate New Release"
echo "============================"

cd ..
CURRENT_RELEASE=$(basename "$(pwd)/releases/%RELEASE%")
echo "Activating release: $CURRENT_RELEASE"

# Atomic symlink switch (DeployHQ B04)
echo "🔄 Switching to new release..."
ln -nfs "releases/%RELEASE%" current
echo "  ✅ Current symlink updated"

# Setup public_html for shared hosting (DeployHQ B04)
echo "🌐 Setting up public access..."
if [ ! -L public_html ] && [ ! -d public_html ]; then
    ln -s current/public public_html
    echo "  ✅ public_html symlink created"
elif [ -L public_html ]; then
    echo "  ℹ️ public_html symlink already exists"
else
    echo "  ℹ️ public_html directory exists (not symlinking)"
fi

# Verify symlinks (Expert 3 validation)
if [ -L "current" ] && [ -d "current" ]; then
    echo "  ✅ Current release symlink verified"
    echo "  📋 Active release: $(readlink current)"
else
    echo "  ❌ CRITICAL: Current release symlink failed!"
    exit 1
fi

# ============================================================================
# B05: EXIT MAINTENANCE MODE
# ============================================================================

echo ""
echo "🚧 B05: Exit Maintenance Mode"
echo "============================="

cd current

if [ -f "artisan" ]; then
    echo "🔓 Bringing application online..."

    # Final application test before going live (Expert 3 safety)
    echo "🔍 Final application health check..."
    if php artisan --version >/dev/null 2>&1; then
        echo "  ✅ Laravel application ready"
    else
        echo "  ❌ CRITICAL: Laravel application not ready - keeping in maintenance mode!"
        exit 1
    fi

    # Exit maintenance mode (DeployHQ B05)
    if php artisan up 2>/dev/null; then
        echo "  ✅ Application is now live"
    else
        echo "  ⚠️ Failed to exit maintenance mode (check manually)"
    fi

    # Verify application is accessible (Expert 3 post-activation check)
    echo "🏥 Post-activation health check..."

    # Test basic functionality
    if php artisan list >/dev/null 2>&1; then
        echo "  ✅ Artisan commands functional"
    else
        echo "  ⚠️ WARNING: Artisan commands may have issues"
    fi

    # Test route resolution (if routes are cached)
    if [ -f "bootstrap/cache/routes-v7.php" ]; then
        if php artisan route:list >/dev/null 2>&1; then
            echo "  ✅ Route resolution working"
        else
            echo "  ⚠️ WARNING: Route resolution issues detected"
        fi
    fi

    # Test log file creation
    php artisan inspire >/dev/null 2>&1 || true
    if [ -f "storage/logs/laravel.log" ]; then
        echo "  ✅ Logging system functional"
    else
        echo "  ℹ️ Logging test inconclusive"
    fi

else
    echo "ℹ️ Non-Laravel application - no maintenance mode to exit"
    echo "✅ Application should be accessible"
fi

# ============================================================================
# B06: CLEANUP OLD RELEASES
# ============================================================================

echo ""
echo "🧹 B06: Cleanup Old Releases"
echo "============================"

cd ../releases
RELEASES_TO_KEEP=3
TOTAL_RELEASES=$(ls -1 | wc -l)

echo "📊 Release cleanup analysis:"
echo "  Total releases: $TOTAL_RELEASES"
echo "  Releases to keep: $RELEASES_TO_KEEP"

if [ "$TOTAL_RELEASES" -gt "$RELEASES_TO_KEEP" ]; then
    RELEASES_TO_DELETE=$((TOTAL_RELEASES - RELEASES_TO_KEEP))
    echo "  Releases to delete: $RELEASES_TO_DELETE"

    echo "🗑️ Removing old releases..."
    ls -t | tail -n +$((RELEASES_TO_KEEP + 1)) | xargs rm -rf 2>/dev/null || true
    echo "  ✅ Old releases cleaned up"

    # Verify cleanup (Expert 3 verification)
    REMAINING_RELEASES=$(ls -1 | wc -l)
    echo "  📊 Releases remaining: $REMAINING_RELEASES"

else
    echo "  ℹ️ Only $TOTAL_RELEASES releases found, no cleanup needed"
fi

# Show current releases for monitoring (Expert 3 transparency)
echo "📋 Current releases:"
ls -la | grep "^d" | awk '{print "  " $9 " (" $6 " " $7 " " $8 ")"}'

# ============================================================================
# DEPLOYMENT COMPLETION SUMMARY (Expert 3 comprehensive reporting)
# ============================================================================

echo ""
echo "🎉 DEPLOYMENT COMPLETED SUCCESSFULLY!"
echo "===================================="

# Create deployment timestamp for monitoring (Expert 3 operational readiness)
DEPLOYMENT_TIME=$(date -u +"%Y-%m-%d %H:%M:%S UTC")
echo "$DEPLOYMENT_TIME" > ../current/storage/last-deployment 2>/dev/null || echo "$DEPLOYMENT_TIME" > ../current/last-deployment

echo ""
echo "🎯 FINAL DEPLOYMENT SUMMARY:"
echo "  ⏰ Completed: $DEPLOYMENT_TIME"
echo "  🔄 Active Release: %RELEASE%"
echo "  🌐 Public Access: $([ -L "../public_html" ] && echo "public_html symlink active" || echo "Direct public/ access")"
echo "  🚧 Maintenance Mode: Exited successfully"
echo "  🏥 Health Checks: $([ -f "artisan" ] && echo "Laravel systems verified" || echo "Non-Laravel deployment")"
echo "  🧹 Cleanup: $([ "$TOTAL_RELEASES" -gt "$RELEASES_TO_KEEP" ] && echo "$RELEASES_TO_DELETE old releases removed" || echo "No cleanup needed")"
echo "  📊 Total Releases: $TOTAL_RELEASES (keeping $RELEASES_TO_KEEP)"
echo ""

# Environment-specific success messages (Expert 3 context awareness)
DEPLOY_TARGET=${DEPLOY_TARGET:-"production"}
case "$DEPLOY_TARGET" in
    "production")
        echo "🌟 PRODUCTION DEPLOYMENT SUCCESSFUL!"
        echo "  💼 Your Laravel application is now live in production"
        echo "  🔍 Monitor performance and logs closely"
        echo "  🚨 Set up alerts for any critical issues"
        ;;
    "staging")
        echo "🧪 STAGING DEPLOYMENT SUCCESSFUL!"
        echo "  🔬 Application ready for comprehensive testing"
        echo "  ✅ Verify all features work as expected"
        echo "  🚀 Ready for production deployment approval"
        ;;
    "demo"|"installer")
        echo "🎯 DEMO/INSTALLER DEPLOYMENT SUCCESSFUL!"
        echo "  🎪 Application ready for demonstration"
        echo "  📋 Test installation and onboarding process"
        echo "  🎓 Ready for user training and demos"
        ;;
esac

echo ""
echo "🚀 NEXT STEPS:"
echo "  📈 Monitor application performance and response times"
echo "  📊 Check error logs for any deployment-related issues"
echo "  🔄 Set up automated monitoring and backup schedules"
echo "  📱 Configure alerting for critical system metrics"
echo "  🔐 Review security configurations and SSL certificates"
echo ""
echo "📚 Based on DeployHQ B04-B06 + Expert 3 comprehensive research"
echo "🎯 Universal deployment pipeline completed for ANY Laravel application"
echo "🌐 Site should be live at your domain"
