# Etapa 1: Construcción (builder)
FROM node:20-alpine AS builder

WORKDIR /app

COPY package*.json ./
RUN npm install

COPY . .
RUN npm run build

# Etapa 2: Producción (nginx)
FROM nginx:stable-alpine

# Copia el build generado al nginx public folder
COPY --from=builder /app/dist /usr/share/nginx/html

# Copia config personalizada de nginx (si la tienes)
COPY nginx.conf /etc/nginx/nginx.conf

# Expone puerto 80
EXPOSE 80

COPY docker/entrypoint.sh /entrypoint.sh
COPY docker/config.template.js /usr/share/nginx/html/config.template.js
RUN chmod +x /entrypoint.sh
CMD ["/entrypoint.sh"]
