#!/bin/bash
# Build script for Custom AI Image Description Generator WordPress plugin
# Creates a distributable zip file

set -e

# Get version from main plugin file
VERSION=$(grep -m1 "Version:" custom-ai-image-description-generator.php | sed 's/.*Version: //' | tr -d '[:space:]')
PLUGIN_NAME="custom-ai-image-description-generator"
ZIP_NAME="${PLUGIN_NAME}-v${VERSION}.zip"

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}Building ${PLUGIN_NAME} v${VERSION}...${NC}"

# Create temp directory
TEMP_DIR=$(mktemp -d)
PLUGIN_DIR="${TEMP_DIR}/${PLUGIN_NAME}"

mkdir -p "$PLUGIN_DIR"

# Copy plugin files (exclude dev files)
cp custom-ai-image-description-generator.php "$PLUGIN_DIR/"
cp README.md "$PLUGIN_DIR/"
cp CHANGELOG.md "$PLUGIN_DIR/"
cp INSTALL.md "$PLUGIN_DIR/"

# Optional: include diagnostic tools (comment out if not wanted)
[ -f diagnostic.php ] && cp diagnostic.php "$PLUGIN_DIR/"
[ -f test-generation.php ] && cp test-generation.php "$PLUGIN_DIR/"
[ -f test-openrouter.php ] && cp test-openrouter.php "$PLUGIN_DIR/"

# Create zip
cd "$TEMP_DIR"
zip -r "$ZIP_NAME" "$PLUGIN_NAME"

# Move zip to original directory
mv "$ZIP_NAME" "$OLDPWD/"

# Cleanup
rm -rf "$TEMP_DIR"

cd "$OLDPWD"

echo -e "${GREEN}Created: ${ZIP_NAME}${NC}"
echo -e "Size: $(du -h "$ZIP_NAME" | cut -f1)"
echo ""
echo "Contents:"
unzip -l "$ZIP_NAME"
