import { defineConfig } from 'vite';
import symfonyPlugin from 'vite-plugin-symfony';
var twigRefreshPlugin = {
    name: 'twig-refresh',
    configureServer: function (_a) {
        var watcher = _a.watcher, ws = _a.ws;
        watcher
            .on('change', function (path) {
            if (path.endsWith('.twig')) {
                console.log("Twig force reload \"".concat(path, "\""));
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
        sourcemap: true,
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
