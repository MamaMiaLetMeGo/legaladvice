import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    build: {
        // Ensure manifest is generated
        manifest: true,
        // Specify the output directory
        outDir: 'public/build',
        // Make the build more verbose for debugging
        minify: true,
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['alpinejs', 'axios']
                }
            }
        }
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            // Ensure Vite knows where to generate the manifest
            manifest: true,
            // Force the manifest to be created even in production
            buildDirectory: 'build'
        }),
    ],
});