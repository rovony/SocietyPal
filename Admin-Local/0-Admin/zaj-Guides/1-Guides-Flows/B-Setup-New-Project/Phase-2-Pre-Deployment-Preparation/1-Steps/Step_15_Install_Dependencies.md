# Step 15: Install Dependencies & V3.3 Universal Tracking System

**Install dependencies with comprehensive change tracking for all project phases (Phase 2, updates, customizations, vendor updates)**

> 📋 **Analysis Source:** V2 Step 10 enhanced with V3.3 Universal Tracking System
>
> 🎯 **Purpose:** Install dependencies with smart tracking system for reproducible builds and comprehensive change management across all project lifecycle phases

---

## **🎯 Phase 1: V3.3 Universal Change Tracking System**

### **1. Initialize Central Tracking System**

**Create universal tracking system for ALL project phases:**

```bash
# Navigate to project root
cd "/Users/malekokour/Zaj_Master/MyApps/MyLaravel_Apps/2_Apps/SocietyPal-Project/SocietyPalApp-Master/SocietyPalApp-Root"

# Create central tracking infrastructure
mkdir -p Admin-Local/Universal-Tracking/{Baselines,Sessions,Reports,Scripts}

# Initialize tracking session
TRACKING_SESSION="phase2-step15-$(date +'%Y%m%d-%H%M%S')"
SESSION_DIR="Admin-Local/Universal-Tracking/Sessions/$TRACKING_SESSION"
mkdir -p "$SESSION_DIR"

echo "🎯 V3.3 Universal Change Tracking System Initialized"
echo "Session: $TRACKING_SESSION" | tee "$SESSION_DIR/session-info.txt"
echo "Purpose: Phase 2 Dependencies Installation" | tee -a "$SESSION_DIR/session-info.txt"
echo "Date: $(date)" | tee -a "$SESSION_DIR/session-info.txt"
```

### **2. Flexible Baseline Recording System**

**Choose baseline method based on project needs (Git Tag OR File List):**

```bash
echo "🔍 Creating flexible baseline record..."

# Universal File Baseline (Works for ALL projects)
echo "📋 Creating comprehensive file baseline..."
find . -type f \
    -not -path "./vendor/*" \
    -not -path "./node_modules/*" \
    -not -path "./.git/*" \
    -not -path "./Admin-Local/Universal-Tracking/*" \
    -not -path "./storage/logs/*" \
    -not -path "./storage/framework/*" \
    > "$SESSION_DIR/baseline-all-files.txt"

# Package States Baseline
if [ -f "package.json" ]; then
    cp package.json "$SESSION_DIR/baseline-package.json"
    [ -f "package-lock.json" ] && cp package-lock.json "$SESSION_DIR/baseline-package-lock.json"
    [ -f ".npmrc" ] && cp .npmrc "$SESSION_DIR/baseline-npmrc.txt"
fi

if [ -f "composer.json" ]; then
    cp composer.json "$SESSION_DIR/baseline-composer.json"
    [ -f "composer.lock" ] && cp composer.lock "$SESSION_DIR/baseline-composer.lock"
fi

# Optional Git Baseline (if git project and user wants it)
if git rev-parse --git-dir > /dev/null 2>&1; then
    echo "📋 Git repository detected"
    read -p "Create git baseline tag for this session? (y/N): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        git add . 2>/dev/null || true
        git commit -m "Pre-Step15 baseline: Before dependencies setup" 2>/dev/null || echo "Nothing to commit"
        GIT_TAG="baseline-step15-$(date +'%Y%m%d-%H%M%S')"
        git tag "$GIT_TAG" 2>/dev/null && echo "✅ Git baseline tag: $GIT_TAG" || echo "Tag creation failed"
        echo "$GIT_TAG" > "$SESSION_DIR/git-baseline-tag.txt"
    fi
    
    # Always record git state for tracking
    git ls-files > "$SESSION_DIR/baseline-git-files.txt" 2>/dev/null || true
    git status --porcelain > "$SESSION_DIR/baseline-git-status.txt" 2>/dev/null || true
fi

echo "✅ Flexible baseline recorded in: $SESSION_DIR"
```

### **3. Create Universal Tracking Scripts**

**Generate reusable tracking scripts for all future use cases:**

```bash
# Create universal change detection script
cat > Admin-Local/Universal-Tracking/Scripts/track_changes.sh << 'EOF'
#!/bin/bash
# Universal Change Tracking Script v3.3
# Use Cases: Phase 2, Updates, Customizations, Vendor Updates, etc.

USAGE="Usage: $0 <session_name> <purpose> [baseline_session]"
SESSION_NAME=${1:-$(date +'%Y%m%d-%H%M%S')}
PURPOSE=${2:-"General Change Tracking"}
BASELINE_SESSION=$3

if [ -z "$SESSION_NAME" ]; then
    echo "$USAGE"
    exit 1
fi

SESSION_DIR="Admin-Local/Universal-Tracking/Sessions/$SESSION_NAME"
mkdir -p "$SESSION_DIR"

echo "🔍 Universal Change Tracking: $SESSION_NAME"
echo "Purpose: $PURPOSE" | tee "$SESSION_DIR/session-info.txt"
echo "Date: $(date)" | tee -a "$SESSION_DIR/session-info.txt"

# Current state snapshot
find . -type f \
    -not -path "./vendor/*" \
    -not -path "./node_modules/*" \
    -not -path "./.git/*" \
    -not -path "./Admin-Local/Universal-Tracking/*" \
    -not -path "./storage/logs/*" \
    -not -path "./storage/framework/*" \
    > "$SESSION_DIR/current-files.txt"

# Git state (if available)
if git rev-parse --git-dir > /dev/null 2>&1; then
    git status --porcelain > "$SESSION_DIR/current-git-status.txt"
    git diff --name-only > "$SESSION_DIR/current-git-changes.txt" 2>/dev/null || true
fi

# Package states
[ -f "package.json" ] && cp package.json "$SESSION_DIR/current-package.json"
[ -f "package-lock.json" ] && cp package-lock.json "$SESSION_DIR/current-package-lock.json"
[ -f "composer.json" ] && cp composer.json "$SESSION_DIR/current-composer.json"
[ -f "composer.lock" ] && cp composer.lock "$SESSION_DIR/current-composer.lock"

# Compare with baseline if provided
if [ -n "$BASELINE_SESSION" ] && [ -d "Admin-Local/Universal-Tracking/Sessions/$BASELINE_SESSION" ]; then
    echo "📊 Comparing with baseline: $BASELINE_SESSION"
    diff "Admin-Local/Universal-Tracking/Sessions/$BASELINE_SESSION/baseline-all-files.txt" \
         "$SESSION_DIR/current-files.txt" \
         > "$SESSION_DIR/files-diff.txt" 2>/dev/null || true
fi

echo "✅ Change tracking completed: $SESSION_DIR"
EOF

chmod +x Admin-Local/Universal-Tracking/Scripts/track_changes.sh

# Create change report generator
cat > Admin-Local/Universal-Tracking/Scripts/generate_report.sh << 'EOF'
#!/bin/bash
# Universal Change Report Generator v3.3

SESSION_NAME=${1:-$(ls Admin-Local/Universal-Tracking/Sessions/ | tail -1)}
SESSION_DIR="Admin-Local/Universal-Tracking/Sessions/$SESSION_NAME"

if [ ! -d "$SESSION_DIR" ]; then
    echo "❌ Session not found: $SESSION_NAME"
    exit 1
fi

REPORT_FILE="Admin-Local/Universal-Tracking/Reports/report-$SESSION_NAME.md"

cat > "$REPORT_FILE" << REPORT_EOF
# Universal Change Report: $SESSION_NAME

**Generated:** $(date)
**Purpose:** $(grep "Purpose:" "$SESSION_DIR/session-info.txt" | cut -d' ' -f2-)

## Summary

### Files Changed
\`\`\`
$(if [ -f "$SESSION_DIR/files-diff.txt" ]; then grep "^>" "$SESSION_DIR/files-diff.txt" | head -10; else echo "No baseline comparison available"; fi)
\`\`\`

### Package Changes
$(if [ -f "$SESSION_DIR/current-package.json" ] && [ -f "$SESSION_DIR/baseline-package.json" ]; then
    echo "**NPM Changes:**"
    diff "$SESSION_DIR/baseline-package.json" "$SESSION_DIR/current-package.json" | head -10 || echo "No package.json changes"
fi)

$(if [ -f "$SESSION_DIR/current-composer.json" ] && [ -f "$SESSION_DIR/baseline-composer.json" ]; then
    echo "**Composer Changes:**"
    diff "$SESSION_DIR/baseline-composer.json" "$SESSION_DIR/current-composer.json" | head -10 || echo "No composer.json changes"
fi)

### Git Status
\`\`\`
$(if [ -f "$SESSION_DIR/current-git-status.txt" ]; then cat "$SESSION_DIR/current-git-status.txt"; else echo "No git status available"; fi)
\`\`\`

## Use Cases
- ✅ Phase 2 Setup Tracking
- ✅ CodeCanyon Vendor Updates
- ✅ Custom Modifications
- ✅ Deployment Preparation
- ✅ Rollback Preparation

REPORT_EOF

echo "✅ Report generated: $REPORT_FILE"
EOF

chmod +x Admin-Local/Universal-Tracking/Scripts/generate_report.sh

echo "✅ Universal tracking scripts created"
```

### **4. CodeCanyon-Safe NPM Configuration**

**Configure .npmrc as development tool to avoid vendor conflicts:**

```bash
echo "🔧 Setting up CodeCanyon-safe NPM configuration..."

if [ -f "package.json" ]; then
    # Create development-friendly .npmrc that won't conflict with CodeCanyon updates
    cat > .npmrc << 'EOF'
# V3.3 Development Configuration - Safe for CodeCanyon Updates
# These settings apply to development workflow only

# Package management
package-lock=true
save-exact=false

# Development workflow
fund=false
audit-level=moderate
progress=true

# Note: Composer already handles exact versions via composer.lock
# NPM flexible versions ensure compatibility with CodeCanyon updates
EOF

    echo "✅ CodeCanyon-safe .npmrc created"
    
    # Record in tracking
    cp .npmrc "$SESSION_DIR/npmrc-created.txt"
    
    # Note about exact versions strategy
    echo "ℹ️ Version Strategy Notes:" | tee -a "$SESSION_DIR/version-strategy-notes.txt"
    echo "- Composer: Uses exact versions via composer.lock (no changes needed)" | tee -a "$SESSION_DIR/version-strategy-notes.txt"
    echo "- NPM: Flexible versions for CodeCanyon compatibility" | tee -a "$SESSION_DIR/version-strategy-notes.txt"
    echo "- Lock files ensure reproducible builds regardless of version ranges" | tee -a "$SESSION_DIR/version-strategy-notes.txt"
else
    echo "ℹ️ No package.json - NPM configuration skipped"
fi
```

---

## **📦 Phase 2: Install Dependencies with Comprehensive Tracking**

### **5. Install PHP Dependencies with Change Detection**

```bash
echo "📦 Installing PHP dependencies with tracking..."

# Before installation tracking
./Admin-Local/Universal-Tracking/Scripts/track_changes.sh "${TRACKING_SESSION}-pre-php" "Before PHP Dependencies"

# Install PHP dependencies (development mode)
composer install --verbose

# After installation tracking
./Admin-Local/Universal-Tracking/Scripts/track_changes.sh "${TRACKING_SESSION}-post-php" "After PHP Dependencies"

# Verify installation
COMPOSER_PACKAGES=$(composer show 2>/dev/null | wc -l | tr -d ' ')
echo "✅ PHP Dependencies Installed: $COMPOSER_PACKAGES packages"
echo "Composer packages: $COMPOSER_PACKAGES" >> "$SESSION_DIR/installation-summary.txt"

# Verify composer.lock
if [ -f "composer.lock" ]; then
    COMPOSER_LOCK_SIZE=$(wc -c < composer.lock)
    echo "✅ composer.lock: $COMPOSER_LOCK_SIZE bytes"
    echo "composer.lock size: $COMPOSER_LOCK_SIZE bytes" >> "$SESSION_DIR/installation-summary.txt"
else
    echo "❌ ERROR: composer.lock not generated"
    exit 1
fi
```

### **6. Install JavaScript Dependencies with Tracking**

```bash
echo "🎨 Installing JavaScript dependencies with tracking..."

if [ -f "package.json" ]; then
    echo "✅ JavaScript build system detected"
    
    # Before JS installation tracking
    ./Admin-Local/Universal-Tracking/Scripts/track_changes.sh "${TRACKING_SESSION}-pre-js" "Before JavaScript Dependencies"
    
    # Install with our safe .npmrc configuration
    npm install
    
    # After JS installation tracking
    ./Admin-Local/Universal-Tracking/Scripts/track_changes.sh "${TRACKING_SESSION}-post-js" "After JavaScript Dependencies"
    
    # Verify installation
    NPM_PACKAGES=$(npm list --depth=0 2>/dev/null | grep -c "├──\|└──" || echo "0")
    echo "✅ JavaScript Dependencies Installed: $NPM_PACKAGES packages"
    echo "NPM packages: $NPM_PACKAGES" >> "$SESSION_DIR/installation-summary.txt"
    
    # Verify package-lock.json
    if [ -f "package-lock.json" ]; then
        PACKAGE_LOCK_SIZE=$(wc -c < package-lock.json)
        echo "✅ package-lock.json: $PACKAGE_LOCK_SIZE bytes"
        echo "package-lock.json size: $PACKAGE_LOCK_SIZE bytes" >> "$SESSION_DIR/installation-summary.txt"
    else
        echo "❌ ERROR: package-lock.json not generated"
        exit 1
    fi
else
    echo "ℹ️ No package.json found - PHP-only project"
    echo "Project type: PHP-only" >> "$SESSION_DIR/installation-summary.txt"
fi
```

---

## **📊 Phase 3: Comprehensive Verification & Documentation**

### **7. Final Installation Verification**

```bash
echo "🔍 Running comprehensive installation verification..."

# Create final tracking snapshot
./Admin-Local/Universal-Tracking/Scripts/track_changes.sh "${TRACKING_SESSION}-final" "Final State After Dependencies"

# Verify all requirements met
VERIFICATION_PASS=true

# Check composer requirements
if [ -f "composer.json" ]; then
    if [ ! -f "composer.lock" ]; then
        echo "❌ composer.lock missing"
        VERIFICATION_PASS=false
    fi
    if [ ! -d "vendor" ]; then
        echo "❌ vendor/ directory missing"
        VERIFICATION_PASS=false
    fi
fi

# Check npm requirements
if [ -f "package.json" ]; then
    if [ ! -f "package-lock.json" ]; then
        echo "❌ package-lock.json missing"
        VERIFICATION_PASS=false
    fi
    if [ ! -d "node_modules" ]; then
        echo "❌ node_modules/ directory missing"
        VERIFICATION_PASS=false
    fi
fi

# Check .npmrc configuration
if [ -f ".npmrc" ]; then
    echo "✅ .npmrc configured for CodeCanyon compatibility"
else
    echo "ℹ️ .npmrc not applicable (PHP-only project)"
fi

if [ "$VERIFICATION_PASS" = true ]; then
    echo "✅ ALL VERIFICATIONS PASSED"
    echo "Status: PASSED" >> "$SESSION_DIR/installation-summary.txt"
else
    echo "❌ VERIFICATION FAILURES DETECTED"
    echo "Status: FAILED" >> "$SESSION_DIR/installation-summary.txt"
    exit 1
fi
```

### **8. Generate Comprehensive Documentation**

```bash
echo "📝 Generating final documentation..."

# Generate change report
./Admin-Local/Universal-Tracking/Scripts/generate_report.sh "$TRACKING_SESSION"

# Create installation summary
cat >> "$SESSION_DIR/installation-summary.txt" << EOF

## V3.3 Universal Tracking Implementation

### Tracking Features Implemented:
- ✅ Flexible baseline recording (Git tags + File lists)
- ✅ Before/after change detection
- ✅ Universal tracking scripts for all phases
- ✅ CodeCanyon-safe configuration
- ✅ Comprehensive change documentation

### Future Use Cases Ready:
- ✅ Phase 2, 3, 4 project phases
- ✅ CodeCanyon vendor updates
- ✅ Custom modifications tracking
- ✅ Deployment preparation
- ✅ Rollback procedures

### Session Files Created:
$(ls -la "$SESSION_DIR" | awk '{print "- " $9}' | grep -v "^- \.$\|^- \.\.$\|^- $")

EOF

# Final status
echo ""
echo "🎯 STEP 15 COMPLETE - V3.3 UNIVERSAL TRACKING IMPLEMENTED"
echo "============================================================"
echo "📦 Dependencies: Installed and verified"
echo "🔍 Tracking System: Universal change detection active"
echo "🛡️ CodeCanyon Safe: .npmrc configured for compatibility"
echo "📊 Documentation: Comprehensive change reports generated"
echo "🚀 Future Ready: Tracking system works for all project phases"
echo ""
echo "📁 Session Data: $SESSION_DIR"
echo "📄 Change Report: Admin-Local/Universal-Tracking/Reports/report-$TRACKING_SESSION.md"
echo ""
```

---

## **🛠️ Universal Tracking Usage Examples**

### **Quick Commands for All Project Phases:**

```bash
# Phase 2: Current step
./Admin-Local/Universal-Tracking/Scripts/track_changes.sh "step16-test" "Step 16 Build Process"

# Future: CodeCanyon Updates
./Admin-Local/Universal-Tracking/Scripts/track_changes.sh "vendor-update-v142" "CodeCanyon Update to v1.42"

# Future: Custom Modifications
./Admin-Local/Universal-Tracking/Scripts/track_changes.sh "custom-feature-auth" "Custom Authentication Feature"

# Future: Deployment Preparation
./Admin-Local/Universal-Tracking/Scripts/track_changes.sh "deploy-prep-prod" "Production Deployment Preparation"

# Generate reports for any session
./Admin-Local/Universal-Tracking/Scripts/generate_report.sh "session-name"
```

### **Shell Aliases for Convenience:**
```bash
# Add to ~/.bashrc for easy access
alias track-changes='./Admin-Local/Universal-Tracking/Scripts/track_changes.sh'
alias track-report='./Admin-Local/Universal-Tracking/Scripts/generate_report.sh'
alias track-status='ls -la Admin-Local/Universal-Tracking/Sessions/ | tail -5'
alias step15-verify='cat Admin-Local/Universal-Tracking/Sessions/*/installation-summary.txt | tail -20'
```

---

## **🚨 Emergency Procedures**

### **Rollback to Baseline:**
```bash
# If git baseline was created
GIT_TAG=$(cat Admin-Local/Universal-Tracking/Sessions/*/git-baseline-tag.txt 2>/dev/null | head -1)
if [ -n "$GIT_TAG" ]; then
    git checkout "$GIT_TAG"
    echo "✅ Rolled back to git baseline: $GIT_TAG"
fi

# Clean dependencies
rm -rf vendor/ node_modules/ composer.lock package-lock.json
echo "✅ Dependencies cleaned - ready for fresh install"
```

### **Verify System State:**
```bash
# Quick system health check
echo "🔍 System State Verification:"
[ -f "composer.json" ] && echo "✅ composer.json exists" || echo "❌ composer.json missing"
[ -f "composer.lock" ] && echo "✅ composer.lock exists" || echo "⚠️ composer.lock missing"
[ -f "package.json" ] && echo "✅ package.json exists" || echo "ℹ️ package.json not found"
[ -f "package-lock.json" ] && echo "✅ package-lock.json exists" || echo "⚠️ package-lock.json missing"
[ -d "vendor" ] && echo "✅ vendor/ directory exists" || echo "❌ vendor/ missing"
[ -d "node_modules" ] && echo "✅ node_modules/ directory exists" || echo "⚠️ node_modules/ missing"
```

---

## **📈 Success Metrics & Implementation Status**

**V3.3 Universal Tracking Implementation:**
- [x] Flexible baseline recording (Git + File lists)
- [x] Central tracking system for all project phases
- [x] CodeCanyon-safe NPM configuration
- [x] Universal change detection scripts
- [x] Comprehensive documentation generation
- [x] Emergency rollback procedures
- [x] Future-proof tracking architecture

**Expected State After Step 15:**
- **Dependencies**: Installed with full change tracking
- **Tracking System**: Active for all future project phases
- **CodeCanyon Compatibility**: Safe configuration preventing conflicts
- **Documentation**: Complete audit trail from baseline
- **Scalability**: Ready for Phase 2, updates, customizations, vendor updates

---

**Next Step:** [Step 16: Test Build Process](Step_16_Test_Build_Process.md) - Now with comprehensive change tracking!
