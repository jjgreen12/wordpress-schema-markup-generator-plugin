@echo off
REM Bulletproof build script using Windows 10+ built-in tar command
REM This creates proper Unix-compatible zip files

echo ========================
echo Creating Schema Stunt Cock Plugin Package
echo ========================

REM 1. Clean previous build artifacts
echo Cleaning previous build files...
if exist "schema-stunt-cock" rd /s /q "schema-stunt-cock"
if exist "schema-stunt-cock.zip" del "schema-stunt-cock.zip"

REM 2. Create plugin package folder and subdirectories
echo Creating plugin package structure...
mkdir "schema-stunt-cock"
mkdir "schema-stunt-cock\css"
mkdir "schema-stunt-cock\js"

REM 3. Copy main plugin file
echo Copying PHP file...
if exist "schema-stunt-cock.php" copy "schema-stunt-cock.php" "schema-stunt-cock\"

REM 4. Copy CSS files to css subdirectory
echo Copying CSS files...
if exist "css\*.css" (
    copy "css\*.css" "schema-stunt-cock\css\"
    echo   CSS files copied successfully
) else (
    echo   No CSS files found
)

REM 5. Copy JavaScript files to js subdirectory  
echo Copying JavaScript files...
if exist "js\*.js" (
    copy "js\*.js" "schema-stunt-cock\js\"
    echo   JS files copied successfully
) else (
    echo   No JS files found
)

REM 6. Copy README
echo Copying README...
if exist "README.md" copy "README.md" "schema-stunt-cock\"

REM 7. Create zip using Windows built-in tar command (Windows 10+)
echo Creating plugin zip file using Windows tar...
tar -a -c -f "schema-stunt-cock.zip" -C "." "schema-stunt-cock"

REM 8. Verify zip file was created
if exist "schema-stunt-cock.zip" (
    echo Zip file created successfully!
    for %%F in ("schema-stunt-cock.zip") do echo Zip file size: %%~zF bytes
) else (
    echo ERROR: Zip file was not created!
    pause
    exit /b 1
)

echo ========================
echo Build complete! Plugin zip file created: schema-stunt-cock.zip
echo You can now install this zip file via WordPress admin panel.
echo ========================

pause