#!/bin/bash

# =============================================================================
# Multi-Branch Synchronization Script for Laravel CodeCanyon Deployment
# =============================================================================
# Purpose: Synchronize current progress across all deployment branches
# Usage: ./multi_branch_sync.sh
# Location: Admin-Local/0-Setup-Operations/1-First-Setup/
# =============================================================================

set -e  # Exit on any error

# Color codes for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Header
echo -e "${CYAN}🔄 Multi-Branch Synchronization Script${NC}"
echo "=============================================="
echo ""

# Step 1: Verify Current Status
echo -e "${BLUE}📊 Current Git Status:${NC}"
git status
echo ""

# Show current branch
echo -e "${YELLOW}🌟 Current Branch:${NC}"
CURRENT_BRANCH=$(git branch --show-current)
echo -e "${GREEN}${CURRENT_BRANCH}${NC}"
echo ""

# List all branches
echo -e "${PURPLE}🌳 All Branches:${NC}"
git branch -a
echo ""

# Step 2: Stage all current changes
echo -e "${BLUE}📦 Staging all current changes...${NC}"
git add .

# Verify staged changes
echo -e "${YELLOW}📋 Staged Changes:${NC}"
git status --short
echo ""

# Step 3: Create checkpoint commit
CHECKPOINT_DATE=$(date +%Y-%m-%d)
CHECKPOINT_MSG="🔄 P1-S10.1: Progress Synced to All Branches - ${CHECKPOINT_DATE}"

echo -e "${GREEN}💾 Creating checkpoint commit: ${CHECKPOINT_MSG}${NC}"
if git diff --cached --quiet; then
    echo -e "${YELLOW}⚠️  No changes to commit${NC}"
else
    git commit -m "${CHECKPOINT_MSG}"
    echo -e "${GREEN}✅ Checkpoint commit created${NC}"
fi
echo ""

# Step 4: Multi-Branch Synchronization
echo -e "${CYAN}🔄 Starting Multi-Branch Synchronization...${NC}"
echo "================================================"
echo ""

# Define branches to sync (excluding vendor/original)
SYNC_BRANCHES=("main" "development" "staging" "production" "customized")

echo -e "${BLUE}📍 Starting from branch: ${CURRENT_BRANCH}${NC}"
echo ""

# Sync each branch
for branch in "${SYNC_BRANCHES[@]}"; do
    echo ""
    echo -e "${PURPLE}🌳 Processing branch: ${branch}${NC}"
    echo "----------------------------------------"
    
    # Check if branch exists locally
    if git show-ref --verify --quiet refs/heads/${branch}; then
        echo -e "${GREEN}✅ Branch ${branch} exists locally - switching and syncing${NC}"
        
        # Switch to branch
        git checkout ${branch}
        
        # Merge current changes (ensuring all branches have the same state)
        if [ "${branch}" != "${CURRENT_BRANCH}" ]; then
            echo -e "${CYAN}🔄 Merging from ${CURRENT_BRANCH} to ${branch}${NC}"
            if git merge ${CURRENT_BRANCH} --no-edit; then
                echo -e "${GREEN}✅ Successfully merged to ${branch}${NC}"
            else
                echo -e "${RED}❌ Merge conflict in ${branch} - manual resolution required${NC}"
                exit 1
            fi
        else
            echo -e "${YELLOW}📍 Already on current branch - no merge needed${NC}"
        fi
        
        echo -e "${GREEN}✅ Branch ${branch} synchronized${NC}"
    else
        echo -e "${YELLOW}❌ Branch ${branch} does not exist locally${NC}"
        
        # Check if it exists on remote
        if git show-ref --verify --quiet refs/remotes/origin/${branch}; then
            echo -e "${CYAN}📡 Branch exists on remote - creating local tracking branch${NC}"
            git checkout -b ${branch} origin/${branch}
            git merge ${CURRENT_BRANCH} --no-edit
            echo -e "${GREEN}✅ Created and synchronized ${branch}${NC}"
        else
            echo -e "${BLUE}🆕 Creating new branch ${branch} from current state${NC}"
            git checkout -b ${branch}
            echo -e "${GREEN}✅ Created new branch ${branch}${NC}"
        fi
    fi
done

# Step 5: Return to original branch
echo ""
echo -e "${CYAN}🏠 Returning to original branch: ${CURRENT_BRANCH}${NC}"
git checkout ${CURRENT_BRANCH}

echo ""
echo -e "${GREEN}🎉 Multi-Branch Synchronization Complete!${NC}"
echo "================================================"
echo ""

# Step 6: Push all synchronized branches
echo -e "${CYAN}📡 Pushing all synchronized branches to remote...${NC}"
echo "================================================"

for branch in "${SYNC_BRANCHES[@]}"; do
    echo ""
    echo -e "${BLUE}📡 Pushing branch: ${branch}${NC}"
    
    # Check if branch exists locally
    if git show-ref --verify --quiet refs/heads/${branch}; then
        # Push with upstream tracking
        if git push -u origin ${branch}; then
            echo -e "${GREEN}✅ Successfully pushed ${branch}${NC}"
        else
            echo -e "${RED}❌ Failed to push ${branch}${NC}"
            # Continue with other branches instead of exiting
        fi
    else
        echo -e "${YELLOW}⚠️  Branch ${branch} does not exist locally - skipping push${NC}"
    fi
done

echo ""
echo -e "${GREEN}🎉 All branch operations complete!${NC}"
echo "================================================"

# Step 7: Final verification
echo ""
echo -e "${CYAN}🔍 Final Verification: Checking all branch statuses...${NC}"
echo "================================================"

# Check each branch's latest commit
for branch in "${SYNC_BRANCHES[@]}"; do
    if git show-ref --verify --quiet refs/heads/${branch}; then
        echo ""
        echo -e "${PURPLE}🌳 Branch: ${branch}${NC}"
        git log --oneline -1 ${branch}
    fi
done

echo ""
echo -e "${BLUE}📡 Remote status summary:${NC}"
git remote show origin | grep -E "(pushes to|Local branch)" | head -10

echo ""
echo -e "${GREEN}✅ Branch synchronization verification complete!${NC}"
echo "================================================"