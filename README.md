wordpress-schema-markup-generator-plugin

# How to create a plugin from this codebase

Step 3: Test the React App Locally
```bash
# Run the development server to test changes
npm run dev
```
Check that all components render correctly and functionality works as expected in the local development environment.
Step 4: Build the React App
Once you're satisfied with the local testing, build the production version:
```bash
# Build the React app for production
npm run build
```
This will create a dist folder with compiled JS and CSS assets that the WordPress plugin will use.
Step 5: Package the Plugin for WordPress
To create a WordPress-compatible ZIP file:

Create a new folder for the plugin package:

```bash
mkdir schema-stunt-cock-plugin
```
Copy all required files to this folder:

```bash
# Copy the main plugin file
cp schema-stunt-cock.php schema-stunt-cock-plugin/

# Copy the compiled assets
cp -r dist/ schema-stunt-cock-plugin/dist/

# Copy any other necessary files (README, etc.)
cp README.md schema-stunt-cock-plugin/
```
Create the ZIP file:

```bash
# On Linux/Mac
zip -r schema-stunt-cock-plugin.zip schema-stunt-cock-plugin/

# On Windows (PowerShell)
Compress-Archive -Path .\schema-stunt-cock-plugin\* -DestinationPath schema-stunt-cock-plugin.zip
```
3. Installation in WordPress

In your WordPress admin, go to Plugins > Add New > Upload Plugin
Select the schema-stunt-cock-plugin.zip file
Click Install Now and then Activate Plugin

# For making updates
Run a clean build:

```bash
# Remove the dist directory and its contents
Remove-Item -Path .\dist -Recurse -Force

# Build the app
npm run build

# Create directories for packaging
New-Item -ItemType Directory -Path .\schema-stunt-cock-plugin\dist\assets -Force

# Copy main plugin file
Copy-Item -Path .\schema-stunt-cock.php -Destination .\schema-stunt-cock-plugin\

# Copy compiled assets
Copy-Item -Path .\dist\assets\index.js -Destination .\schema-stunt-cock-plugin\dist\assets\
Copy-Item -Path .\dist\assets\index.css -Destination .\schema-stunt-cock-plugin\dist\assets\
```

For creating a ZIP file in PowerShell, you can use:

```powershell
# Method 1: Using Compress-Archive (PowerShell 5.0+)
Compress-Archive -Path .\schema-stunt-cock-plugin\* -DestinationPath .\schema-stunt-cock-plugin.zip -Force

```
Deactivate and delete the current plugin in WordPress
Upload and activate the new plugin ZIP