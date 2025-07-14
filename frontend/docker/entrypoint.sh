#!/bin/sh

# Inyecta la variable en el config.js
envsubst '${API_BASE_URL}' < /usr/share/nginx/html/config.template.js > /usr/share/nginx/html/config.js

# Arranca nginx
exec nginx -g 'daemon off;'
