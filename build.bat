@echo off
REM Clean build script for Schema Stunt Cock WordPress plugin (Windows version)
REM This script will:
REM 1. Clean previous build files
REM 2. Build the React app
REM 3. Create the plugin package with correct folder structure
REM 4. Create a zip file ready for WordPress installation

echo ========================
echo Building Schema Stunt Cock Plugin...
echo ========================

REM 1. Clean previous build artifacts
echo Cleaning previous build files...
if exist "dist" rd /s /q "dist"
if exist "schema-stunt-cock-plugin" rd /s /q "schema-stunt-cock-plugin"
if exist "schema-stunt-cock-plugin.zip" del "schema-stunt-cock-plugin.zip"

REM 2. Build the React app
echo Building React application...
call npm run build

REM 3. Create plugin package folder
echo Creating plugin package...
mkdir schema-stunt-cock-plugin
mkdir schema-stunt-cock-plugin\dist

REM 4. Copy plugin files
echo Copying files to plugin package...
REM Copy main plugin file
copy schema-stunt-cock.php schema-stunt-cock-plugin\

REM Copy built assets with correct structure
copy dist\index.js schema-stunt-cock-plugin\dist\
copy dist\index.css schema-stunt-cock-plugin\dist\

REM Copy README
copy README.md schema-stunt-cock-plugin\

REM 5. Create zip file
echo Creating plugin zip file...
powershell -command "Compress-Archive -Path .\schema-stunt-cock-plugin\* -DestinationPath .\schema-stunt-cock-plugin.zip -Force"

echo ========================
echo Build complete! Plugin zip file created: schema-stunt-cock-plugin.zip
echo You can now install this zip file via WordPress admin panel.
echo ========================