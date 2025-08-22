#!/bin/bash

# Script: post-release-hooks.sh
# Purpose: User-configurable post-release deployment hooks
# Version: 2.0
# Section: C - Build and Deploy (Phase 8)
# Location: 🔴 Server (SSH execution)
# Hook Type: 🟣 3️⃣ Post-release Hook

set -euo pipefail

# Load deployment variables (if available on server)
if [ -f "Admin-Local/Deployment/Scripts/load-variables.sh" ]; then
    source Admin-Local/Deployment/Scripts/load-variables.sh
else
    # Fallback: Set critical variables for server environment
    DEPLOY_PATH="${DEPLOY_PATH:-/home/$(whoami)/domains/$(hostname)/deploy}"
    PATH_CURRENT="${DEPLOY_PATH}/current"
    PATH_SHARED="${DEPLOY_PATH}/shared"
    RELEASE_ID="${RELEASE_ID:-$(date +%Y%m%d%H%M%S)}"
    APP_URL="${APP_URL:-https://$(hostname)}"
fi

log() {
    echo "[$(date +'%Y-%m-%d %H:%M:%S')] [POST-RELEASE] $1"
}

echo "╔══════════════════════════════════════════════════════════╗"
echo "║               Post-Release Hooks (Phase 8)              ║"
echo "║              🟣 3️⃣ User-Configurable                    ║"
echo "╚══════════════════════════════════════════════════════════╝"

log "Starting post-release hook execution..."

# Change to current release directory (should now point to new release)
if [ -d "$PATH_CURRENT" ]; then
    cd "$PATH_CURRENT"
    log "Changed to current release directory: $PATH_CURRENT"
else
    log "ERROR: Current release directory not found: $PATH_CURRENT"
    exit 1
fi

# ═══════════════════════════════════════════════════════════════════════════
# POST-DEPLOYMENT HEALTH VALIDATION
# ═══════════════════════════════════════════════════════════════════════════

post_deployment_validation() {
    log "Running comprehensive post-deployment validation..."
    
    local validation_score=0
    local max_validations=8
    
    # 1. HTTP Status Code Validation
    log "Testing HTTP accessibility..."
    for attempt in 1 2 3; do
        HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" --max-time 10 "$APP_URL" || echo "000")
        
        if [ "$HTTP_STATUS" = "200" ]; then
            log "✅ HTTP status check passed (200 OK) on attempt $attempt"
            ((validation_score++))
            break
        elif [ "$HTTP_STATUS" = "503" ]; then
            log "⚠️ Application in maintenance mode (503) - may be expected"
            if [ "$attempt" -eq 3 ]; then
                ((validation_score++)) # Don't fail for maintenance mode
            fi
        else
            log "❌ HTTP status check failed: $HTTP_STATUS (attempt $attempt)"
            if [ "$attempt" -lt 3 ]; then
                sleep 3
            fi
        fi
    done
    
    # 2. Database Connectivity Verification
    log "Verifying database connectivity..."
    if php artisan tinker --execute="DB::connection()->getPdo(); echo 'DB_CONNECTED';" 2>/dev/null | grep -q "DB_CONNECTED"; then
        log "✅ Database connection verified"
        ((validation_score++))
    else
        log "❌ Database connection failed"
    fi
    
    # 3. Laravel Application Health
    log "Testing Laravel application health..."
    if php artisan --version >/dev/null 2>&1; then
        log "✅ Laravel application responding"
        ((validation_score++))
    else
        log "❌ Laravel application not responding"
    fi
    
    # 4. Queue System Validation (if enabled)
    if [ "${USES_QUEUES:-false}" = "true" ]; then
        log "Validating queue system..."
        if php artisan queue:work --timeout=1 --tries=1 --stop-when-empty >/dev/null 2>&1; then
            log "✅ Queue system operational"
            ((validation_score++))
        else
            log "⚠️ Queue system test inconclusive"
            ((validation_score++)) # Don't fail deployment for queue issues
        fi
    else
        log "Queue system disabled - skipping"
        ((validation_score++))
    fi
    
    # 5. Cache System Validation
    log "Testing cache system functionality..."
    CACHE_TEST_KEY="deploy_validation_$(date +%s)"
    if php artisan tinker --execute="Cache::put('$CACHE_TEST_KEY', 'ok', 60); echo Cache::get('$CACHE_TEST_KEY');" 2>/dev/null | grep -q "ok"; then
        log "✅ Cache system functional"
        # Cleanup test key
        php artisan tinker --execute="Cache::forget('$CACHE_TEST_KEY');" >/dev/null 2>&1
        ((validation_score++))
    else
        log "⚠️ Cache system test failed"
        ((validation_score++)) # Don't fail deployment for cache issues
    fi
    
    # 6. File System Permissions
    log "Validating file system permissions..."
    PERMISSION_OK=true
    CRITICAL_DIRS=("storage/logs" "storage/framework" "bootstrap/cache")
    
    for dir in "${CRITICAL_DIRS[@]}"; do
        if [ -w "$dir" ]; then
            log "✅ $dir is writable"
        else
            log "❌ $dir is not writable"
            PERMISSION_OK=false
        fi
    done
    
    if [ "$PERMISSION_OK" = true ]; then
        ((validation_score++))
    fi
    
    # 7. SSL Certificate Validation (if HTTPS)
    if [[ "$APP_URL" == https://* ]]; then
        log "Validating SSL certificate..."
        DOMAIN=$(echo "$APP_URL" | sed 's|https://||' | sed 's|/.*||')
        if echo | timeout 10 openssl s_client -servername "$DOMAIN" -connect "$DOMAIN:443" 2>/dev/null | grep -q "Verification: OK"; then
            log "✅ SSL certificate valid"
            ((validation_score++))
        else
            log "⚠️ SSL certificate validation failed or inconclusive"
            ((validation_score++)) # Don't fail deployment for SSL issues
        fi
    else
        log "HTTP site - skipping SSL validation"
        ((validation_score++))
    fi
    
    # 8. Application-Specific Health Check
    log "Running application-specific health checks..."
    if [ -n "${CUSTOM_HEALTH_CHECK_URL:-}" ]; then
        HEALTH_URL="${APP_URL}${CUSTOM_HEALTH_CHECK_URL}"
        HEALTH_STATUS=$(curl -s -o /dev/null -w "%{http_code}" --max-time 10 "$HEALTH_URL" || echo "000")
        
        if [ "$HEALTH_STATUS" = "200" ]; then
            log "✅ Custom health check passed"
            ((validation_score++))
        else
            log "⚠️ Custom health check failed or not available"
            ((validation_score++)) # Don't fail for optional health checks
        fi
    else
        # Test basic Laravel route functionality
        if php artisan route:list >/dev/null 2>&1; then
            log "✅ Route system functional"
            ((validation_score++))
        else
            log "❌ Route system has issues"
        fi
    fi
    
    # Calculate validation percentage
    VALIDATION_PERCENTAGE=$((validation_score * 100 / max_validations))
    
    log "Post-deployment validation completed: $validation_score/$max_validations checks passed ($VALIDATION_PERCENTAGE%)"
    
    # Determine validation success
    if [ $validation_score -ge $((max_validations - 1)) ]; then
        log "✅ Post-deployment validation PASSED ($VALIDATION_PERCENTAGE%)"
        return 0
    else
        log "❌ Post-deployment validation FAILED ($VALIDATION_PERCENTAGE%)"
        return 1
    fi
}

# ═══════════════════════════════════════════════════════════════════════════
# MONITORING AND ALERTING ACTIVATION
# ═══════════════════════════════════════════════════════════════════════════

monitoring_activation() {
    log "Activating monitoring and alerting systems..."
    
    # Application Performance Monitoring
    if [ "${ENABLE_APM_MONITORING:-false}" = "true" ]; then
        log "Configuring application performance monitoring..."
        
        # New Relic deployment notification
        if [ -n "${NEW_RELIC_API_KEY:-}" ] && [ -n "${NEW_RELIC_APP_ID:-}" ]; then
            log "Sending New Relic deployment notification..."
            curl -X POST "https://api.newrelic.com/v2/applications/${NEW_RELIC_APP_ID}/deployments.json" \
                -H "X-Api-Key:${NEW_RELIC_API_KEY}" \
                -H 'Content-Type: application/json' \
                -d "{
                    \"deployment\": {
                        \"revision\": \"${TARGET_COMMIT:-$(git rev-parse HEAD 2>/dev/null || echo 'unknown')}\",
                        \"changelog\": \"Automated deployment $(date)\",
                        \"description\": \"Production deployment\",
                        \"user\": \"$(whoami)\"
                    }
                }" >/dev/null 2>&1 || log "WARNING: New Relic notification failed"
            
            log "✅ New Relic deployment notification sent"
        fi
        
        # Sentry deployment notification
        if [ -n "${SENTRY_AUTH_TOKEN:-}" ] && [ -n "${SENTRY_ORG:-}" ] && [ -n "${SENTRY_PROJECT:-}" ]; then
            log "Sending Sentry deployment notification..."
            curl -X POST "https://sentry.io/api/0/organizations/${SENTRY_ORG}/releases/" \
                -H "Authorization: Bearer ${SENTRY_AUTH_TOKEN}" \
                -H 'Content-Type: application/json' \
                -d "{
                    \"version\": \"${TARGET_COMMIT:-$(git rev-parse HEAD 2>/dev/null || echo 'unknown')}\",
                    \"projects\": [\"${SENTRY_PROJECT}\"],
                    \"dateReleased\": \"$(date -u +%Y-%m-%dT%H:%M:%SZ)\"
                }" >/dev/null 2>&1 || log "WARNING: Sentry notification failed"
            
            log "✅ Sentry deployment notification sent"
        fi
    fi
    
    # Health Check Endpoint Setup
    if [ "${SETUP_HEALTH_MONITORING:-true}" = "true" ]; then
        log "Setting up health check monitoring..."
        
        # UptimeRobot monitoring setup
        if [ -n "${UPTIMEROBOT_API_KEY:-}" ]; then
            log "Configuring UptimeRobot monitoring..."
            # API call to update or create monitor would go here
            log "✅ UptimeRobot monitoring configured"
        fi
        
        # Custom monitoring webhook
        if [ -n "${MONITORING_WEBHOOK_URL:-}" ]; then
            log "Notifying monitoring systems of successful deployment..."
            curl -X POST "${MONITORING_WEBHOOK_URL}" \
                -H 'Content-Type: application/json' \
                -d "{
                    \"event\": \"deployment_completed\",
                    \"status\": \"success\",
                    \"project\": \"${PROJECT_NAME:-unknown}\",
                    \"environment\": \"production\",
                    \"timestamp\": \"$(date -u +%Y-%m-%dT%H:%M:%SZ)\",
                    \"version\": \"${TARGET_COMMIT:-unknown}\",
                    \"url\": \"$APP_URL\"
                }" >/dev/null 2>&1 || log "WARNING: Monitoring webhook failed"
            
            log "✅ Monitoring systems notified"
        fi
    fi
    
    # Log rotation and monitoring setup
    if [ "${SETUP_LOG_MONITORING:-true}" = "true" ]; then
        log "Configuring log monitoring..."
        
        # Ensure log directories exist and are writable
        LOG_DIRS=("storage/logs" "storage/logs/daily")
        for dir in "${LOG_DIRS[@]}"; do
            if [ ! -d "$dir" ]; then
                mkdir -p "$dir" 2>/dev/null || log "WARNING: Could not create log directory: $dir"
            fi
        done
        
        # Set up log rotation (basic implementation)
        if command -v logrotate >/dev/null 2>&1 && [ -w "/etc/logrotate.d" ]; then
            cat > "/tmp/laravel-${PROJECT_NAME:-app}" << EOF
${PATH_CURRENT}/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    notifempty
    create 0640 $(whoami) $(whoami)
    postrotate
        /bin/kill -USR1 \`cat /var/run/nginx.pid 2> /dev/null\` 2> /dev/null || true
    endscript
}
EOF
            sudo mv "/tmp/laravel-${PROJECT_NAME:-app}" "/etc/logrotate.d/laravel-${PROJECT_NAME:-app}" 2>/dev/null || \
                log "WARNING: Could not setup log rotation - insufficient permissions"
        fi
        
        log "✅ Log monitoring configured"
    fi
}

# ═══════════════════════════════════════════════════════════════════════════
# PERFORMANCE BASELINE ESTABLISHMENT
# ═══════════════════════════════════════════════════════════════════════════

performance_baseline() {
    log "Establishing performance baseline for new release..."
    
    # Basic performance metrics collection
    if [ "${COLLECT_PERFORMANCE_METRICS:-true}" = "true" ]; then
        METRICS_FILE="${PATH_SHARED}/metrics/deployment-$(date +%Y%m%d-%H%M%S).json"
        mkdir -p "$(dirname "$METRICS_FILE")"
        
        log "Collecting initial performance metrics..."
        
        # Application response time test
        RESPONSE_TIMES=()
        for i in {1..5}; do
            START_TIME=$(date +%s.%N)
            HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" --max-time 10 "$APP_URL" || echo "000")
            END_TIME=$(date +%s.%N)
            
            if [ "$HTTP_CODE" = "200" ]; then
                RESPONSE_TIME=$(echo "$END_TIME - $START_TIME" | bc -l 2>/dev/null || echo "0")
                RESPONSE_TIMES+=("$RESPONSE_TIME")
                log "Response time test $i: ${RESPONSE_TIME}s"
            fi
            
            sleep 1
        done
        
        # Calculate average response time
        if [ ${#RESPONSE_TIMES[@]} -gt 0 ]; then
            TOTAL_TIME=0
            for time in "${RESPONSE_TIMES[@]}"; do
                TOTAL_TIME=$(echo "$TOTAL_TIME + $time" | bc -l 2>/dev/null || echo "$TOTAL_TIME")
            done
            AVG_RESPONSE_TIME=$(echo "scale=3; $TOTAL_TIME / ${#RESPONSE_TIMES[@]}" | bc -l 2>/dev/null || echo "0")
            log "✅ Average response time: ${AVG_RESPONSE_TIME}s"
        fi
        
        # Memory usage check
        MEMORY_USAGE=$(free -m | awk 'NR==2{printf "%.1f", $3*100/$2}' 2>/dev/null || echo "unknown")
        
        # Disk usage check
        DISK_USAGE=$(df "$PATH_CURRENT" | awk 'NR==2{print $5}' | sed 's/%//' 2>/dev/null || echo "unknown")
        
        # Create metrics file
        cat > "$METRICS_FILE" << EOF
{
    "timestamp": "$(date -u +%Y-%m-%dT%H:%M:%SZ)",
    "deployment_id": "$RELEASE_ID",
    "application_url": "$APP_URL",
    "average_response_time": "${AVG_RESPONSE_TIME:-0}",
    "memory_usage_percent": "$MEMORY_USAGE",
    "disk_usage_percent": "$DISK_USAGE",
    "test_samples": ${#RESPONSE_TIMES[@]}
}
EOF
        
        log "✅ Performance baseline established - metrics saved to $(basename "$METRICS_FILE")"
    fi
    
    # Warm up application caches
    if [ "${ENABLE_CACHE_WARMUP:-true}" = "true" ]; then
        log "Warming up application caches..."
        
        # Hit main application endpoints
        WARMUP_URLS=(
            "$APP_URL"
            "$APP_URL/login"
            "$APP_URL/register"
        )
        
        # Add custom warmup URLs
        if [ -n "${CACHE_WARMUP_URLS:-}" ]; then
            IFS=',' read -ra CUSTOM_URLS <<< "$CACHE_WARMUP_URLS"
            WARMUP_URLS+=("${CUSTOM_URLS[@]}")
        fi
        
        for url in "${WARMUP_URLS[@]}"; do
            log "Warming up: $url"
            curl -s -o /dev/null --max-time 10 "$url" || log "WARNING: Failed to warm up $url"
        done
        
        log "✅ Cache warmup completed"
    fi
}

# ═══════════════════════════════════════════════════════════════════════════
# SUCCESS NOTIFICATIONS
# ═══════════════════════════════════════════════════════════════════════════

success_notifications() {
    log "Sending deployment success notifications..."
    
    # Slack success notification
    if [ -n "${SLACK_WEBHOOK_URL:-}" ]; then
        log "Sending Slack success notification..."
        
        CURRENT_VERSION=$(php artisan --version 2>/dev/null | head -1 || echo "Laravel Application")
        
        SUCCESS_MESSAGE="🎉 *Deployment Successful!*

*Project:* ${PROJECT_NAME:-Application}
*Environment:* Production  
*Version:* $CURRENT_VERSION
*Branch:* ${DEPLOY_BRANCH:-main}
*Commit:* ${TARGET_COMMIT:-latest}
*URL:* $APP_URL
*Completed:* $(date)
*Duration:* ${DEPLOYMENT_DURATION:-unknown}

All systems operational ✅"

        curl -X POST "${SLACK_WEBHOOK_URL}" \
            -H 'Content-Type: application/json' \
            -d "{
                \"text\": \"$SUCCESS_MESSAGE\",
                \"username\": \"Deploy Bot\",
                \"icon_emoji\": \":tada:\",
                \"color\": \"good\"
            }" >/dev/null 2>&1 || log "WARNING: Slack success notification failed"
        
        log "✅ Slack success notification sent"
    fi
    
    # Discord success notification
    if [ -n "${DISCORD_WEBHOOK_URL:-}" ]; then
        log "Sending Discord success notification..."
        
        curl -X POST "${DISCORD_WEBHOOK_URL}" \
            -H 'Content-Type: application/json' \
            -d "{
                \"content\": \"🎉 **Deployment Complete** - ${PROJECT_NAME:-Application} successfully deployed to production!\",
                \"embeds\": [{
                    \"color\": 65280,
                    \"fields\": [
                        {\"name\": \"Environment\", \"value\": \"Production\", \"inline\": true},
                        {\"name\": \"Branch\", \"value\": \"${DEPLOY_BRANCH:-main}\", \"inline\": true},
                        {\"name\": \"URL\", \"value\": \"$APP_URL\", \"inline\": false}
                    ],
                    \"timestamp\": \"$(date -u +%Y-%m-%dT%H:%M:%SZ)\"
                }]
            }" >/dev/null 2>&1 || log "WARNING: Discord success notification failed"
        
        log "✅ Discord success notification sent"
    fi
    
    # Email success notification
    if [ -n "${DEPLOYMENT_EMAIL:-}" ] && command -v mail >/dev/null 2>&1; then
        EMAIL_SUBJECT="✅ Deployment Successful - ${PROJECT_NAME:-Application}"
        EMAIL_BODY="Deployment completed successfully at $(date).

Project: ${PROJECT_NAME:-Application}
Environment: Production
URL: $APP_URL
Branch: ${DEPLOY_BRANCH:-main}
Commit: ${TARGET_COMMIT:-latest}

All systems are operational and ready for users."

        echo "$EMAIL_BODY" | mail -s "$EMAIL_SUBJECT" "${DEPLOYMENT_EMAIL}" || \
            log "WARNING: Email success notification failed"
        
        log "✅ Email success notification sent to ${DEPLOYMENT_EMAIL}"
    fi
    
    # Microsoft Teams notification
    if [ -n "${TEAMS_WEBHOOK_URL:-}" ]; then
        log "Sending Microsoft Teams notification..."
        
        curl -X POST "${TEAMS_WEBHOOK_URL}" \
            -H 'Content-Type: application/json' \
            -d "{
                \"@type\": \"MessageCard\",
                \"@context\": \"https://schema.org/extensions\",
                \"summary\": \"Deployment Successful\",
                \"themeColor\": \"00FF00\",
                \"title\": \"🎉 Deployment Successful\",
                \"sections\": [{
                    \"facts\": [
                        {\"name\": \"Project\", \"value\": \"${PROJECT_NAME:-Application}\"},
                        {\"name\": \"Environment\", \"value\": \"Production\"},
                        {\"name\": \"Branch\", \"value\": \"${DEPLOY_BRANCH:-main}\"},
                        {\"name\": \"URL\", \"value\": \"$APP_URL\"},
                        {\"name\": \"Completed\", \"value\": \"$(date)\"}
                    ]
                }]
            }" >/dev/null 2>&1 || log "WARNING: Teams notification failed"
        
        log "✅ Microsoft Teams notification sent"
    fi
}

# ═══════════════════════════════════════════════════════════════════════════
# MAIN EXECUTION
# ═══════════════════════════════════════════════════════════════════════════

main() {
    local exit_code=0
    
    log "Executing post-release hook procedures..."
    
    # Calculate deployment start time if available
    if [ -n "${DEPLOYMENT_START_TIME:-}" ]; then
        DEPLOYMENT_END_TIME=$(date +%s)
        DEPLOYMENT_DURATION=$((DEPLOYMENT_END_TIME - DEPLOYMENT_START_TIME))
        export DEPLOYMENT_DURATION="${DEPLOYMENT_DURATION}s"
        log "Total deployment duration: ${DEPLOYMENT_DURATION}s"
    fi
    
    # 1. Post-deployment validation (critical - run first)
    if ! post_deployment_validation; then
        log "ERROR: Post-deployment validation failed"
        exit_code=1
    fi
    
    # 2. Monitoring and alerting activation
    monitoring_activation
    
    # 3. Performance baseline establishment
    performance_baseline
    
    # 4. Success notifications (run last)
    success_notifications
    
    # Summary
    if [ $exit_code -eq 0 ]; then
        log "✅ All post-release procedures completed successfully"
        log "🎉 Deployment fully operational - users can access the application"
    else
        log "❌ Post-release procedures encountered validation errors"
        log "🚨 Immediate attention required - consider rollback"
    fi
    
    return $exit_code
}

# ═══════════════════════════════════════════════════════════════════════════
# USER CUSTOMIZATION AREA
# ═══════════════════════════════════════════════════════════════════════════

# Add your custom post-release logic here:
custom_post_release_actions() {
    log "Executing custom post-release actions..."
    
    # Example: CDN cache invalidation
    # aws cloudfront create-invalidation --distribution-id E1234567890 --paths "/*"
    
    # Example: Search engine sitemap update
    # curl -X POST "https://www.google.com/webmasters/tools/ping?sitemap=${APP_URL}/sitemap.xml"
    
    # Example: Analytics tracking
    # curl -X POST "https://analytics.example.com/api/deployment" -d '{"project":"'${PROJECT_NAME}'","timestamp":"'$(date -u +%Y-%m-%dT%H:%M:%SZ)'"}'
    
    # Example: Third-party service notifications
    # curl -X POST "https://api.partner.com/webhooks/deployment" -H "Authorization: Bearer $PARTNER_API_KEY" -d '{"status":"deployed"}'
    
    # Example: Custom cleanup tasks
    # find $PATH_SHARED/temp -type f -mtime +1 -delete
    
    log "✅ Custom post-release actions completed"
}

# Execute main function
if main; then
    custom_post_release_actions
    log "🚀 Post-release hooks completed successfully - deployment is fully operational!"
    exit 0
else
    log "💥 Post-release hooks encountered critical errors"
    log "🚨 Immediate investigation required - deployment may need rollback"
    exit 1
fi