import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Auto-detect API base URL from the browser's current hostname
// This means you NEVER need to update .env when your IP changes
const _currentHost = window.location.hostname || 'localhost';
window.__API_URL__ = `http://${_currentHost}:8000`;
localStorage.setItem('api_url', window.__API_URL__);

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
window.Pusher = Pusher;

function createEchoInstance() {
    return new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: _currentHost,
        wsPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
        wssPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'http') === 'https',
        enabledTransports: ['ws', 'wss'],
        authEndpoint: window.__API_URL__ + '/broadcasting/auth',
        auth: {
            headers: {
                Authorization: 'Bearer ' + localStorage.getItem('auth_token'),
            },
        },
    });
}

// Only connect Echo when user is authenticated (has token)
// This prevents connection timeout errors on public pages
if (localStorage.getItem('auth_token')) {
    try {
        window.Echo = createEchoInstance();
    } catch (e) {
        console.warn('WebSocket connection unavailable:', e.message);
    }
}

// Call this after login to reconnect Echo with the fresh token
window.reconnectEcho = function () {
    try {
        if (window.Echo) {
            window.Echo.disconnect();
        }
        window.Echo = createEchoInstance();
    } catch (e) {
        console.warn('WebSocket reconnection failed:', e.message);
    }
};
