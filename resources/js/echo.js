import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

console.log('🔌 Echo Configuration:');
console.log('  APP URL:', window.location.origin);
console.log('  Reverb Host:', import.meta.env.VITE_REVERB_HOST);
console.log('  Reverb Port:', import.meta.env.VITE_REVERB_PORT);
console.log('  Reverb Scheme:', import.meta.env.VITE_REVERB_SCHEME);
console.log('  Force TLS: false (using ws:// instead of wss://)');

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: false,  // CRITICAL: Force plain WebSocket (ws://) not secure (wss://)
    enabledTransports: ['ws', 'wss'],
});
