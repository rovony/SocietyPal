#!/bin/bash

echo "🔄 Updating Investment Protection Documentation..."

# Get current timestamp
TIMESTAMP=$(date '+%Y-%m-%d %H:%M:%S')

# Update documentation timestamp
find . -name "*.md" -exec sed -i.bak "s/Generated: .*/Generated: $TIMESTAMP/" {} \;

# Cleanup backup files
find . -name "*.bak" -delete

# Generate new customization summary
echo "📊 Regenerating customization analysis..."
bash ../Step-19-Files/generate_investment_documentation.sh

echo "✅ Documentation update completed"
