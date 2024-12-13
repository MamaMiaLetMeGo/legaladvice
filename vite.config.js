import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    // The Laravel plugin handles integration between Vite and Laravel
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    
    // Production build configuration ensures optimal asset delivery
    build: {
        // Ensure assets are built with the correct base URL for production
        base: process.env.APP_URL ? '/' : '',
        
        // Create a manifest file that Laravel can use to load the correct asset versions
        manifest: true,
        
        // Specify the output directory for built assets
        outDir: 'public/build',
        
        // Configure how our code is chunked and optimized
        rollupOptions: {
            output: {
                manualChunks: {
                    // Group third-party libraries separately for better caching
                    vendor: ['alpinejs', 'axios']
                }
            }
        },
        
        // Generate sourcemaps to help debug production issues
        sourcemap: true,
        
        // Prevent warnings about large chunks
        chunkSizeWarningLimit: 1000
    },
    
    // Development server configuration
    server: {
        // Allow connections from any host (important for development)
        host: '0.0.0.0',
        
        // Let Digital Ocean handle SSL termination
        https: false,
        
        // Configure Hot Module Replacement
        hmr: {
            // Increase timeout for slower connections
            timeout: 5000,
            
            // Ensure HMR works through Digital Ocean's proxy
            host: process.env.APP_URL ? new URL(process.env.APP_URL).host : 'localhost'
        }
    }
});