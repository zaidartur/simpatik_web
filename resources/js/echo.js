import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

const getMeta = (name) => document.head.querySelector(`meta[name="${name}"]`)?.content;

const reverbKey = import.meta.env.VITE_REVERB_APP_KEY || getMeta('reverb-key') || '';
const reverbHost = import.meta.env.VITE_REVERB_HOST || getMeta('reverb-host') || window.location.hostname;
const reverbPort = import.meta.env.VITE_REVERB_PORT || getMeta('reverb-port') || 8080;
const reverbScheme = import.meta.env.VITE_REVERB_SCHEME || getMeta('reverb-scheme') || (window.location.protocol === 'https:' ? 'https' : 'http');

if (reverbKey) {
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: reverbKey,
        wsHost: reverbHost,
        wsPort: Number(reverbPort) || 80,
        wssPort: Number(reverbPort) || 443,
        forceTLS: reverbScheme === 'https',
        enabledTransports: ['ws', 'wss'],
    });
}
