@echo off
REM Simple build script for Schema Stunt Cock WordPress plugin
REM This script creates a plugin package with PHP file, CSS, and JS files

echo ========================
echo Creating Schema Stunt Cock Plugin Package
echo ========================

REM 1. Clean previous build artifacts
echo Cleaning previous build files...
if exist "schema-stunt-cock-plugin" rd /s /q "schema-stunt-cock-plugin"
if exist "schema-stunt-cock-plugin.zip" del "schema-stunt-cock-plugin.zip"

REM 2. Create plugin package folder and subdirectories
echo Creating plugin package structure...
mkdir schema-stunt-cock-plugin
mkdir schema-stunt-cock-plugin\css
mkdir schema-stunt-cock-plugin\js

REM 3. Copy main plugin file
echo Copying PHP file...
copy schema-stunt-cock.php schema-stunt-cock-plugin\

REM 4. Copy CSS files
echo Copying CSS files...
copy css\*.css schema-stunt-cock-plugin\css\

REM 5. Copy JavaScript files
echo Copying JavaScript files...
copy js\*.js schema-stunt-cock-plugin\js\

REM 6. Copy README
echo Copying README...
if exist "README.md" copy README.md schema-stunt-cock-plugin\

REM 7. Create zip file
echo Creating plugin zip file...
powershell -command "Compress-Archive -Path .\schema-stunt-cock-plugin\* -DestinationPath .\schema-stunt-cock-plugin.zip -Force"

echo ========================
echo Build complete! Plugin zip file created: schema-stunt-cock-plugin.zip
echo You can now install this zip file via WordPress admin panel.
echo ========================

pause