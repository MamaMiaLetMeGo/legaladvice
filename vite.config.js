import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            // Tell the Laravel plugin where to find assets
            buildDirectory: 'build'
        }),
    ],
    build: {
        // Tell Vite to put files in the public/build directory
        outDir: 'public/build',
        // Generate the manifest file
        manifest: true,
        // Make sure the manifest goes in the right place
        rollupOptions: {
            input: {
                app: 'resources/js/app.js',
                styles: 'resources/css/app.css'
            }
        }
    }
});