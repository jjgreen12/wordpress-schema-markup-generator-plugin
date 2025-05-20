import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';

export default defineConfig({
  plugins: [react()],
  build: {
    // Output to the dist directory
    outDir: 'dist',
    // Disable asset hashing for predictable file names
    rollupOptions: {
      output: {
        entryFileNames: 'index.js',
        chunkFileNames: '[name].js',
        assetFileNames: (assetInfo) => {
          // Handle possible undefined name
          const name = assetInfo.name || '';
          if (name.endsWith('.css')) {
            return 'index.css';
          }
          return '[name].[ext]';
        }
      }
    }
  },
  // Match WordPress plugin structure
  base: './'
});