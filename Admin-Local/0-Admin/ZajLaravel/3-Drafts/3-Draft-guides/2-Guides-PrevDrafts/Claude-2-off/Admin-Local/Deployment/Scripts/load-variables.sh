#!/bin/bash

# Load deployment configuration
CONFIG_FILE="Admin-Local/Deployment/Configs/deployment-variables.json"

if [ ! -f "$CONFIG_FILE" ]; then
    echo "❌ Configuration file not found: $CONFIG_FILE"
    exit 1
fi

# Export as environment variables
export PROJECT_NAME=$(jq -r '.project.name' $CONFIG_FILE)
export PATH_LOCAL_MACHINE=$(jq -r '.paths.local_machine' $CONFIG_FILE)
export PATH_SERVER=$(jq -r '.paths.server_deploy' $CONFIG_FILE)
export PATH_PUBLIC=$(jq -r '.paths.server_public' $CONFIG_FILE)
export GITHUB_REPO=$(jq -r '.repository.url' $CONFIG_FILE)
export DEPLOY_BRANCH=$(jq -r '.repository.deploy_branch' $CONFIG_FILE)
export PHP_VERSION=$(jq -r '.versions.php' $CONFIG_FILE)
export COMPOSER_VERSION=$(jq -r '.versions.composer' $CONFIG_FILE)
export NODE_VERSION=$(jq -r '.versions.node' $CONFIG_FILE)
export BUILD_LOCATION=$(jq -r '.deployment.build_location' $CONFIG_FILE)
export HOSTING_TYPE=$(jq -r '.hosting.type' $CONFIG_FILE)

echo "✅ Variables loaded for project: $PROJECT_NAME"
echo "📁 Local Path: $PATH_LOCAL_MACHINE"
echo "🌐 Repository: $GITHUB_REPO"
echo "🔧 Build Location: $BUILD_LOCATION"