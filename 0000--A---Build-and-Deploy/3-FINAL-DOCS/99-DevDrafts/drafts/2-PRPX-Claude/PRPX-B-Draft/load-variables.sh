#!/bin/bash

# Script: load-variables.sh
# Purpose: Load deployment configuration variables from JSON config
# Version: 2.0
# Section: Universal - All sections use this
# Location: 🟢🟡🔴 All environments

set -euo pipefail

# Script directory detection
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
CONFIG_FILE="${SCRIPT_DIR}/../Configs/deployment-variables.json"

# Log function with timestamp
log() {
    echo "[$(date +'%Y-%m-%d %H:%M:%S')] $1"
}

# Error handling function
error_exit() {
    log "ERROR: $1"
    exit 1
}

# Check if jq is available
if ! command -v jq >/dev/null 2>&1; then
    error_exit "jq is required but not installed. Please install jq first."
fi

# Check if configuration file exists
if [ ! -f "$CONFIG_FILE" ]; then
    error_exit "Configuration file not found: $CONFIG_FILE"
fi

# Validate JSON syntax
if ! jq empty "$CONFIG_FILE" 2>/dev/null; then
    error_exit "Invalid JSON in configuration file: $CONFIG_FILE"
fi

log "Loading deployment configuration..."

# Export project information
export PROJECT_NAME=$(jq -r '.project.name // ""' "$CONFIG_FILE")
export PROJECT_TYPE=$(jq -r '.project.type // "laravel"' "$CONFIG_FILE")
export HAS_FRONTEND=$(jq -r '.project.has_frontend // true' "$CONFIG_FILE")
export FRONTEND_FRAMEWORK=$(jq -r '.project.frontend_framework // ""' "$CONFIG_FILE")
export USES_QUEUES=$(jq -r '.project.uses_queues // false' "$CONFIG_FILE")

# Export path variables
export PATH_LOCAL_MACHINE=$(jq -r '.paths.local_machine // ""' "$CONFIG_FILE")
export PATH_SERVER=$(jq -r '.paths.server_deploy // ""' "$CONFIG_FILE")
export PATH_SERVER_DOMAIN=$(jq -r '.paths.server_domain // ""' "$CONFIG_FILE")
export PATH_PUBLIC=$(jq -r '.paths.server_public // ""' "$CONFIG_FILE")
export PATH_BUILDER_VM=$(jq -r '.paths.builder_vm // ""' "$CONFIG_FILE")

# Export repository information
export GITHUB_REPO=$(jq -r '.repository.url // ""' "$CONFIG_FILE")
export DEPLOY_BRANCH=$(jq -r '.repository.branch // "main"' "$CONFIG_FILE")
export TARGET_COMMIT=$(jq -r '.repository.commit_end // ""' "$CONFIG_FILE")

# Export version requirements
export PHP_VERSION=$(jq -r '.versions.php // "8.2"' "$CONFIG_FILE")
export COMPOSER_VERSION=$(jq -r '.versions.composer // "2"' "$CONFIG_FILE")
export NODE_VERSION=$(jq -r '.versions.node // "18"' "$CONFIG_FILE")

# Export deployment configuration
export DEPLOYMENT_STRATEGY=$(jq -r '.deployment.strategy // "manual"' "$CONFIG_FILE")
export BUILD_LOCATION=$(jq -r '.deployment.build_location // "local"' "$CONFIG_FILE")
export KEEP_RELEASES=$(jq -r '.deployment.keep_releases // 5' "$CONFIG_FILE")
export HEALTH_CHECK_URL=$(jq -r '.deployment.health_check_url // ""' "$CONFIG_FILE")

# Export hosting information
export HOSTING_TYPE=$(jq -r '.hosting.type // ""' "$CONFIG_FILE")
export HAS_ROOT_ACCESS=$(jq -r '.hosting.has_root_access // false' "$CONFIG_FILE")
export EXEC_ENABLED=$(jq -r '.hosting.exec_enabled // true' "$CONFIG_FILE")

# Determine build path based on strategy
case "$BUILD_LOCATION" in
    "local")
        export PATH_BUILDER="$PATH_LOCAL_MACHINE/build-tmp"
        ;;
    "server")
        export PATH_BUILDER="$PATH_SERVER/build-tmp"
        ;;
    "vm")
        export PATH_BUILDER="${BUILD_SERVER_PATH:-/tmp/build}"
        ;;
    *)
        export PATH_BUILDER="$PATH_LOCAL_MACHINE/build-tmp"
        ;;
esac

# Set derived paths
export PATH_RELEASES="$PATH_SERVER/releases"
export PATH_CURRENT="$PATH_SERVER/current"
export PATH_SHARED="$PATH_SERVER/shared"
export PATH_BACKUPS="$PATH_SERVER/backups"

# Set deployment workspace
export DEPLOY_WORKSPACE="${PATH_LOCAL_MACHINE}/Admin-Local/Deployment"

# Generate unique identifiers for this deployment
export DEPLOYMENT_ID="deploy-$(date +%Y%m%d%H%M%S)"
export RELEASE_ID="$(date +%Y%m%d%H%M%S)"

# Validate critical paths
if [ -z "$PATH_LOCAL_MACHINE" ]; then
    error_exit "PATH_LOCAL_MACHINE is not configured in deployment-variables.json"
fi

if [ -z "$PATH_SERVER" ]; then
    error_exit "PATH_SERVER is not configured in deployment-variables.json"
fi

if [ -z "$PROJECT_NAME" ]; then
    error_exit "PROJECT_NAME is not configured in deployment-variables.json"
fi

# Success message
log "✅ Variables loaded successfully for project: $PROJECT_NAME"
log "📁 Local Path: $PATH_LOCAL_MACHINE"
log "📁 Server Path: $PATH_SERVER"
log "📁 Builder Path: $PATH_BUILDER"
log "🎯 Build Strategy: $BUILD_LOCATION"
log "🚀 Deployment Strategy: $DEPLOYMENT_STRATEGY"
log "🆔 Release ID: $RELEASE_ID"

# Validate environment setup
if [ ! -d "$PATH_LOCAL_MACHINE" ]; then
    error_exit "Local machine path does not exist: $PATH_LOCAL_MACHINE"
fi

if [ ! -f "$PATH_LOCAL_MACHINE/composer.json" ]; then
    error_exit "Laravel project not found at: $PATH_LOCAL_MACHINE"
fi

log "🔧 Environment validation passed"