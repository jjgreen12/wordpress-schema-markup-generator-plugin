import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [react()],
  build: {
    // Output to the dist directory
    outDir: 'dist',
    // Disable asset hashing for predictable file names
    rollupOptions: {
      output: {
        // Place entry point JS at dist/index.js
        entryFileNames: 'index.js',
        // No code splitting for better WP compatibility
        chunkFileNames: '[name].js',
        // Place all assets directly in dist folder
        assetFileNames: (info) => {
          const name = info.name || '';
          
          // Force CSS to be named index.css
          if (name.endsWith('.css')) {
            return 'index.css';
          }
          
          // All other assets keep their names
          return `[name].[ext]`;
        }
      }
    }
  },
  // Use root-relative paths
  base: './'
});