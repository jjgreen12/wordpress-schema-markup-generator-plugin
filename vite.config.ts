import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';

export default defineConfig({
  plugins: [react()],
  build: {
    // Ensure assets are placed in the correct folder structure
    outDir: 'dist',
    // Generate with static asset names instead of hashed names
    assetsDir: '', // Changed from 'assets' to ensure files are in the root dist folder
    rollupOptions: {
      output: {
        // Use predictable file names
        entryFileNames: `assets/[name].js`,
        chunkFileNames: `assets/[name].js`,
        assetFileNames: `assets/[name].[ext]`
      }
    }
  },
  // Match WordPress plugin structure
  base: './'
});