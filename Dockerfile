FROM dunglas/frankenphp:1.9-php8.3

USER root

# Install ekstensi PHP tambahan
RUN curl -sSLf https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions -o /usr/local/bin/install-php-extensions \
    && chmod +x /usr/local/bin/install-php-extensions \
    && install-php-extensions gd pdo_mysql

# Tambahkan konfigurasi PHP custom
RUN echo "max_execution_time = 120" > /usr/local/etc/php/conf.d/99-custom.ini

USER www-data
WORKDIR /app

# CMD: Jalankan artisan dengan max_execution_time lebih besar sebelum start FrankenPHP
CMD sh -c "php -d max_execution_time=120 artisan optimize:clear || true && exec frankenphp run --config /etc/frankenphp/Caddyfile"
