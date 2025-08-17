#!/bin/bash
# detect-project-context.sh
# Detects Laravel JS tech stack, module system, build tool, and outputs JSON context

set -e

PROJECT_ROOT="$(pwd)"
OUTPUT_FILE="$PROJECT_ROOT/Admin-Local/1-Current-Project/3-Customization/2-Setup/customization-context.json"

# Detect JS framework
if grep -q 'vue' "$PROJECT_ROOT/package.json"; then
  JS_FRAMEWORK="vue"
elif grep -q 'react' "$PROJECT_ROOT/package.json"; then
  JS_FRAMEWORK="react"
elif grep -q 'inertia' "$PROJECT_ROOT/package.json"; then
  JS_FRAMEWORK="inertia"
elif grep -q 'jquery' "$PROJECT_ROOT/package.json"; then
  JS_FRAMEWORK="vanilla-js"
else
  JS_FRAMEWORK="blade-only"
fi

# Detect module system
if grep -q '"type": "module"' "$PROJECT_ROOT/package.json"; then
  MODULE_SYSTEM="esm"
else
  MODULE_SYSTEM="commonjs"
fi

# Detect build tool
if [ -f "$PROJECT_ROOT/vite.config.js" ] || [ -f "$PROJECT_ROOT/vite.config.ts" ]; then
  BUILD_TOOL="vite"
elif [ -f "$PROJECT_ROOT/webpack.mix.js" ]; then
  BUILD_TOOL="mix"
elif [ -f "$PROJECT_ROOT/webpack.custom.cjs" ]; then
  BUILD_TOOL="webpack-custom"
else
  BUILD_TOOL="none"
fi

# Output JSON
cat <<EOF > "$OUTPUT_FILE"
{
  "js_framework": "$JS_FRAMEWORK",
  "module_system": "$MODULE_SYSTEM",
  "build_tool": "$BUILD_TOOL"
}
EOF

echo "[Step 17] Project context detected and written to $OUTPUT_FILE"
