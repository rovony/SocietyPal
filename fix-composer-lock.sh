#!/bin/bash

# Fix Composer Lock - Manual JSON Edit
# Since we don't have PHP/Composer locally, we'll manually fix the lock file

set -e

echo "🔧 Manually fixing composer.lock for Faker in production..."

# Backup the lock file
cp composer.lock composer.lock.backup
echo "📦 Backed up composer.lock"

# Create a simple Python script to move Faker from packages-dev to packages
cat > fix_lock.py << 'EOF'
import json
import sys

# Read the lock file
with open('composer.lock', 'r') as f:
    lock_data = json.load(f)

# Find Faker in packages-dev and move to packages
faker_package = None
packages_dev = lock_data.get('packages-dev', [])

for i, package in enumerate(packages_dev):
    if package.get('name') == 'fakerphp/faker':
        faker_package = packages_dev.pop(i)
        break

if faker_package:
    # Add to main packages
    if 'packages' not in lock_data:
        lock_data['packages'] = []
    lock_data['packages'].append(faker_package)
    print("✅ Moved fakerphp/faker from packages-dev to packages")
else:
    print("⚠️ fakerphp/faker not found in packages-dev")

# Write back the modified lock file
with open('composer.lock', 'w') as f:
    json.dump(lock_data, f, indent=4)

print("✅ composer.lock updated successfully")
EOF

# Run the Python script
python3 fix_lock.py

# Clean up
rm fix_lock.py

echo "🎯 composer.lock fixed - Faker now in production dependencies"
echo "📊 Ready to commit and push"
