import { defineConfig, ViteDevServer } from 'vite';
import symfonyPlugin from 'vite-plugin-symfony';

const twigRefreshPlugin = {
  name: 'twig-refresh',
  configureServer(server: ViteDevServer) {
    server.watcher
      .on('change', (path: any) => {
        if (path.endsWith('.twig')) {
          console.log(`Twig force reload "${path}"`);
          server.ws.send({
            type: 'full-reload'
          });
        }
      })
      .add('src/UserInterface/**/*.twig');
  }
};

export default defineConfig({
  plugins: [twigRefreshPlugin, symfonyPlugin()],
  root: './assets',
  base: '/',
  server: {
    watch: {
      disableGlobbing: false
    }
  },
  build: {
    sourcemap: true,
    outDir: '../public/build',
    rollupOptions: {
      input: {
        'index.tsx': './assets/index.tsx'
      }
    }
  }
});
