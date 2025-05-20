#!/bin/bash

# Clean build script for Schema Stunt Cock WordPress plugin
# This script will:
# 1. Clean previous build files
# 2. Build the React app
# 3. Create the plugin package with correct folder structure
# 4. Create a zip file ready for WordPress installation

# Exit on any error
set -e

echo "========================"
echo "Building Schema Stunt Cock Plugin..."
echo "========================"

# 1. Clean previous build artifacts
echo "Cleaning previous build files..."
rm -rf dist
rm -rf schema-stunt-cock-plugin
rm -f schema-stunt-cock-plugin.zip

# 2. Build the React app
echo "Building React application..."
npm run build

# 3. Create plugin package folder
echo "Creating plugin package..."
mkdir -p schema-stunt-cock-plugin/dist

# 4. Copy plugin files
echo "Copying files to plugin package..."
# Copy main plugin file
cp schema-stunt-cock.php schema-stunt-cock-plugin/

# Copy built assets with correct structure
cp dist/index.js schema-stunt-cock-plugin/dist/
cp dist/index.css schema-stunt-cock-plugin/dist/

# Copy README
cp README.md schema-stunt-cock-plugin/

# 5. Create zip file
echo "Creating plugin zip file..."
zip -r schema-stunt-cock-plugin.zip schema-stunt-cock-plugin/

echo "========================"
echo "Build complete! Plugin zip file created: schema-stunt-cock-plugin.zip"
echo "You can now install this zip file via WordPress admin panel."
echo "========================"