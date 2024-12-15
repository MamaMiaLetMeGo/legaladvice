/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */
// Import required libraries
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import axios from 'axios';

// Set up axios globally
window.axios = axios;

// Configure default headers for axios
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Get CSRF token from meta tag
const token = document.querySelector('meta[name="csrf-token"]');

// If CSRF token exists, configure axios to use it
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found');
}

// Make Pusher available globally
window.Pusher = Pusher;

// Set up Laravel Echo with proper authentication
if (window.pusherKey) {
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: window.pusherKey,
        cluster: window.pusherCluster,
        forceTLS: true,
        // Add authentication headers for private channels
        auth: {
            headers: {
                'X-CSRF-TOKEN': token ? token.content : '',
                'X-Requested-With': 'XMLHttpRequest'
            }
        },
        // Add error handling for connection issues
        enabledTransports: ['ws', 'wss']
    });

    // Set up global error handling for Echo
    window.Echo.connector.pusher.connection.bind('error', (err) => {
        console.error('Pusher connection error:', err);
    });

    // Log successful connection in development
    window.Echo.connector.pusher.connection.bind('connected', () => {
        console.log('Successfully connected to Pusher');
    });
} else {
    console.warn('Pusher configuration not found');
}

// Add a global axios response interceptor to handle common errors
window.axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response?.status === 419) {
            console.error('CSRF token mismatch. Attempting to refresh page to get new token.');
            // Optionally refresh the page to get a new token
            // window.location.reload();
        }
        return Promise.reject(error);
    }
);
/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// import Pusher from 'pusher-js';
// window.Pusher = Pusher;

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: import.meta.env.VITE_PUSHER_APP_KEY,
//     cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
//     wsHost: import.meta.env.VITE_PUSHER_HOST ?? `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
//     wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
//     wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
//     forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
//     enabledTransports: ['ws', 'wss'],
// });
