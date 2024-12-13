import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    // Base configuration for the Laravel plugin
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    
    // Production-specific optimizations
    build: {
        // Improve chunking strategy for better caching
        rollupOptions: {
            output: {
                manualChunks: {
                    // Group vendor dependencies separately
                    vendor: ['alpinejs', 'axios']
                }
            }
        },
        // Ensure we're generating sourcemaps for production debugging
        sourcemap: true,
        // Optimize chunk size warnings
        chunkSizeWarningLimit: 1000
    },
    
    // Optimize dev server for Digital Ocean's environment
    server: {
        // Allow connections from all hosts (important for DO's proxy setup)
        host: 'db-mysql-nyc3-03426-do-user-8506940-0.j.db.ondigitalocean.com',
        // Explicitly set HTTPS to false as DO handles SSL
        https: false,
        // Increase HMR timeout for slower connections
        hmr: {
            timeout: 5000
        }
    }
});


