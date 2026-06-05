FROM php:8.2-cli

# Install mysqli extension
RUN docker-php-ext-install mysqli

WORKDIR /app

COPY . .

EXPOSE $PORT

CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-10000}"]