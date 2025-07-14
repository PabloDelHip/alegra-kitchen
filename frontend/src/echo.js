import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

const echo = new Echo({
  broadcaster: 'pusher',
  key: 'local', // Debe coincidir con PUSHER_APP_KEY
  wsHost: '127.0.0.1',
  wsPort: 8080,
  forceTLS: false,
  encrypted: false,
  disableStats: true,
  enabledTransports: ['ws'], // Sólo WebSocket
});

export default echo;
