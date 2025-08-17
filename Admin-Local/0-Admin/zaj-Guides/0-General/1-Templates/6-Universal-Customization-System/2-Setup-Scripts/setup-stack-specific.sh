#!/bin/bash

# Universal Stack-Specific Setup Script
# This script detects the project stack and applies appropriate configurations

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Get the directory of this script
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
UNIVERSAL_SYSTEM_ROOT="$(dirname "$SCRIPT_DIR")"
PROJECT_ROOT="${PROJECT_ROOT:-$(pwd)}"

echo -e "${BLUE}🎯 Universal Stack-Specific Setup${NC}"
echo "======================================="

# Parse command line arguments
WORKFLOW=""
CONTEXT=""
OUTPUT_DOCS=""

while [[ $# -gt 0 ]]; do
    case $1 in
        --workflow=*)
            WORKFLOW="${1#*=}"
            shift
            ;;
        --context=*)
            CONTEXT="${1#*=}"
            shift
            ;;
        --output-docs=*)
            OUTPUT_DOCS="${1#*=}"
            shift
            ;;
        *)
            echo -e "${RED}Unknown parameter: $1${NC}"
            exit 1
            ;;
    esac
done

echo -e "${BLUE}Workflow:${NC} $WORKFLOW"
echo -e "${BLUE}Context:${NC} $CONTEXT"
echo -e "${BLUE}Documentation Output:${NC} $OUTPUT_DOCS"
echo ""

# Run detection
echo -e "${YELLOW}🔍 Detecting project context...${NC}"
DETECTION_RESULT=$(bash "$UNIVERSAL_SYSTEM_ROOT/1-Detection-Scripts/detect-project-context.sh")
echo "$DETECTION_RESULT"

# Parse detection results
JS_FRAMEWORK=$(echo "$DETECTION_RESULT" | jq -r '.js_framework // "blade-only"')
MODULE_SYSTEM=$(echo "$DETECTION_RESULT" | jq -r '.module_system // "commonjs"')
BUILD_TOOL=$(echo "$DETECTION_RESULT" | jq -r '.build_tool // "mix"')

echo -e "${GREEN}Detected:${NC}"
echo -e "  JS Framework: ${BLUE}$JS_FRAMEWORK${NC}"
echo -e "  Module System: ${BLUE}$MODULE_SYSTEM${NC}"
echo -e "  Build Tool: ${BLUE}$BUILD_TOOL${NC}"
echo ""

# Apply stack-specific configuration
echo -e "${YELLOW}⚙️ Applying stack-specific configuration...${NC}"
bash "$UNIVERSAL_SYSTEM_ROOT/2-Setup-Scripts/setup-context-aware-configs.sh" \
    --js-framework="$JS_FRAMEWORK" \
    --module-system="$MODULE_SYSTEM" \
    --build-tool="$BUILD_TOOL"

# Generate documentation if output path provided
if [ -n "$OUTPUT_DOCS" ]; then
    echo -e "${YELLOW}📝 Generating project-specific documentation...${NC}"
    mkdir -p "$OUTPUT_DOCS"
    
    # Copy appropriate stack guide
    GUIDE_NAME=""
    case $JS_FRAMEWORK in
        "vue") GUIDE_NAME="step17-vue.md" ;;
        "react") GUIDE_NAME="step17-react.md" ;;
        "inertia") GUIDE_NAME="step17-inertia.md" ;;
        *) GUIDE_NAME="step17-blade-only.md" ;;
    esac
    
    if [ -f "$UNIVERSAL_SYSTEM_ROOT/4-Stack-Guides/$GUIDE_NAME" ]; then
        cp "$UNIVERSAL_SYSTEM_ROOT/4-Stack-Guides/$GUIDE_NAME" "$OUTPUT_DOCS/stack-specific-guide.md"
        echo -e "${GREEN}✅ Stack guide copied: $GUIDE_NAME${NC}"
    fi
    
    # Generate context summary
    cat > "$OUTPUT_DOCS/setup-context.json" << EOF
{
    "workflow": "$WORKFLOW",
    "context": "$CONTEXT", 
    "detection_timestamp": "$(date -u +"%Y-%m-%dT%H:%M:%SZ")",
    "detected_stack": {
        "js_framework": "$JS_FRAMEWORK",
        "module_system": "$MODULE_SYSTEM", 
        "build_tool": "$BUILD_TOOL"
    },
    "universal_system_version": "1.0.0"
}
EOF
    echo -e "${GREEN}✅ Context summary generated${NC}"
fi

echo ""
echo -e "${GREEN}🎉 Stack-specific setup completed successfully!${NC}"
