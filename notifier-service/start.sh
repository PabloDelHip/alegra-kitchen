#!/bin/sh

php artisan reverb:start &
REVERB_PID=$!

php artisan notifier:listen-prepared
LISTENER_EXIT_CODE=$?

echo "🛑 Listener finalizó con código $LISTENER_EXIT_CODE, deteniendo Reverb..."
kill $REVERB_PID

exit $LISTENER_EXIT_CODE
