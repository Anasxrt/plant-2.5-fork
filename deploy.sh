#!/bin/bash
# =============================================================================
# Plant Theme - Production Deploy Script
# =============================================================================
# This script builds the theme for production and pushes to master branch.
# It ensures only production-ready code is deployed.
# =============================================================================

set -e  # Exit on error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${GREEN}============================================${NC}"
echo -e "${GREEN}  Plant Theme - Production Deploy Script${NC}"
echo -e "${GREEN}============================================${NC}"
echo ""

# Step 1: Check for node_modules and install if needed
echo -e "${YELLOW}[1/5] Checking dependencies...${NC}"
if [ ! -d "node_modules" ]; then
    echo "node_modules not found. Running npm install..."
    npm install
else
    echo "Dependencies already installed."
fi

# Step 2: Build for production
echo ""
echo -e "${YELLOW}[2/5] Building for production...${NC}"
echo "Running: npm run build (production mode with drop_console)"
npm run build

# Verify build output exists
if [ -f "css/mobile.min.css" ] && [ -f "css/desktop.min.css" ]; then
    echo -e "${GREEN}✓ Production CSS files generated successfully${NC}"
else
    echo -e "${RED}✗ Error: Production CSS files not found${NC}"
    exit 1
fi

# Step 3: Check git status
echo ""
echo -e "${YELLOW}[3/5] Checking git status...${NC}"
git status --short

# Step 4: Add all changes
echo ""
echo -e "${YELLOW}[4/5] Staging changes...${NC}"
git add -A

# Step 5: Commit and push
echo ""
echo -e "${YELLOW}[5/5] Committing and pushing to master...${NC}"

# Get current version from package.json
VERSION=$(node -p "require('./package.json').version" 2>/dev/null || echo "unknown")

# Create commit message with timestamp
COMMIT_MSG="Production deploy v${VERSION} - $(date '+%Y-%m-%d %H:%M:%S')"

git commit -m "$COMMIT_MSG"
echo -e "${GREEN}Committed: ${COMMIT_MSG}${NC}"

# Push to master
git push origin master

echo ""
echo -e "${GREEN}============================================${NC}"
echo -e "${GREEN}  ✓ Production deploy completed successfully!${NC}"
echo -e "${GREEN}============================================${NC}"
echo ""
echo "Build output:"
echo "  - css/mobile.min.css"
echo "  - css/desktop.min.css"
echo "  - css/*.min.css (other minified assets)"
echo ""
echo "Run 'git log --oneline -1' to verify the commit."