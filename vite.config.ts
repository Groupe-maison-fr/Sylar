import { defineConfig } from 'vite';
export default defineConfig({
  root: './assets',
  base: '/',
  server: {
    watch: {
      disableGlobbing: true,
    },
  },
  build: {
    manifest: true,
    sourcemap: true,
    outDir: '../public/build',
    rollupOptions: {
      input: {
        index: './assets/index.tsx',
      },
    },
  },
});
