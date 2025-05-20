import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';

export default defineConfig({
  plugins: [react()],
  build: {
    // Ensure assets are placed in the correct folder structure
    outDir: 'dist',
    // Generate with static asset names instead of hashed names
    assetsDir: 'assets',
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