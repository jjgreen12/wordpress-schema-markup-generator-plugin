@echo off
REM Simple build script for Schema Stunt Cock WordPress plugin
REM This script creates a plugin package with PHP file, CSS, and JS files

echo ========================
echo Creating Schema Stunt Cock Plugin Package
echo ========================

REM 1. Clean previous build artifacts
echo Cleaning previous build files...
if exist "schema-stunt-cock" rd /s /q "schema-stunt-cock"
if exist "schema-stunt-cock.zip" del "schema-stunt-cock.zip"

REM 2. Create plugin package folder and subdirectories
echo Creating plugin package structure...
mkdir schema-stunt-cock
mkdir schema-stunt-cock\css
mkdir schema-stunt-cock\js

REM 3. Copy main plugin file
echo Copying PHP file...
copy schema-stunt-cock.php schema-stunt-cock\

REM 4. Copy CSS files
echo Copying CSS files...
copy css\*.css schema-stunt-cock\css\

REM 5. Copy JavaScript files
echo Copying JavaScript files...
copy js\*.js schema-stunt-cock\js\

REM 6. Copy README
echo Copying README...
if exist "README.md" copy README.md schema-stunt-cock\

REM 7. Fix directory separator for Linux
echo Fixing directory separators for Linux compatibility...
powershell -command "$content = Get-Content 'schema-stunt-cock.php' -Raw; $content = $content -replace '\\\\', '/'; Set-Content 'schema-stunt-cock\schema-stunt-cock.php' -Value $content"

REM 8. Create zip file
echo Creating plugin zip file...
powershell -command "Compress-Archive -Path .\schema-stunt-cock\* -DestinationPath .\schema-stunt-cock.zip -Force"

echo ========================
echo Build complete! Plugin zip file created: schema-stunt-cock.zip
echo You can now install this zip file via WordPress admin panel.
echo ========================

pause