import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

const currentHost = window.location.hostname || import.meta.env.VITE_REVERB_HOST || '127.0.0.1';
const configuredPort = import.meta.env.VITE_REVERB_PORT ?? 8080;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: currentHost,
    wsPort: configuredPort,
    wssPort: configuredPort,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});
