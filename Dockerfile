FROM php:8.3-cli
WORKDIR /app
COPY . .
RUN apt-get update && apt-get install -y libsqlite3-dev && docker-php-ext-install pdo_sqlite
EXPOSE 8000
CMD ["php", "-S", "0.0.0.0:8000"]
