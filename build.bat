@echo off
REM Simplified build script that only copies the PHP file without building assets
REM This is for the fallback solution that embeds CSS and JS directly

echo ========================
echo Creating Schema Stunt Cock Plugin Package
echo ========================

REM 1. Clean previous build artifacts
echo Cleaning previous build files...
if exist "schema-stunt-cock-plugin" rd /s /q "schema-stunt-cock-plugin"
if exist "schema-stunt-cock-plugin.zip" del "schema-stunt-cock-plugin.zip"

REM 2. Create plugin package folder
echo Creating plugin package...
mkdir schema-stunt-cock-plugin

REM 3. Copy main plugin file
echo Copying PHP file...
copy schema-stunt-cock.php schema-stunt-cock-plugin\

REM 4. Copy README
echo Copying README...
copy README.md schema-stunt-cock-plugin\

REM 5. Create zip file
echo Creating plugin zip file...
powershell -command "Compress-Archive -Path .\schema-stunt-cock-plugin\* -DestinationPath .\schema-stunt-cock-plugin.zip -Force"

echo ========================
echo Build complete! Plugin zip file created: schema-stunt-cock-plugin.zip
echo You can now install this zip file via WordPress admin panel.
echo ========================

REM Pause to see any errors
pause