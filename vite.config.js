import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            buildDirectory: 'build'
        }),
    ],
    build: {
        manifest: true,
        outDir: 'public/build',
        assetsDir: 'assets',
        rollupOptions: {
            input: ['resources/css/app.css', 'resources/js/app.js']
        }
    },
    server: {
        hmr: {
            host: 'localhost'
        }
    }
});