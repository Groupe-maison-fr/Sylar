import { defineConfig } from 'vite';
import symfonyPlugin from 'vite-plugin-symfony';

const twigRefreshPlugin = {
  name: 'twig-refresh',
  configureServer({ watcher, ws }) {
    watcher
      .on('change', (path) => {
        if (path.endsWith('.twig')) {
          console.log(`Twig force reload "${path}"`);
          ws.send({
            type: 'full-reload'
          });
        }
      })
      .add('src/UserInterface/**/*.twig');
  }
};

export default defineConfig({
  plugins: [
    twigRefreshPlugin,
    symfonyPlugin()
  ],
  root: './assets',
  base: '/',
  server: {
    watch: {
      disableGlobbing: false
    }
  },
  build: {
    // manifest: true,
    // assetsDir: '',
    outDir: '../public/build',
    rollupOptions: {
      input: {
        'index.tsx': './assets/index.tsx',
        // 'app.css': './assets/App.css',
        // 'primeicons.css': './node_modules/primeicons/primeicons.css',
      }
    }
  }
});
