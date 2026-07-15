FROM php:8.3-apache

# System deps + PHP extensions required by sentrion
# (pdo_pgsql/pgsql for PostgreSQL, curl + mbstring per README requirements)
RUN apt-get update && apt-get install -y --no-install-recommends \
        libpq-dev \
        libcurl4-openssl-dev \
        libonig-dev \
        pkg-config \
        cron \
    && docker-php-ext-install pdo pdo_pgsql pgsql mbstring curl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Apache modules the app's .htaccess relies on
RUN a2enmod rewrite headers

# Allow .htaccess overrides (off by default in the base Apache config)
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

WORKDIR /var/www/html

COPY . /var/www/html

# Writable dirs the app needs at runtime (installer, rule engine, logs, sessions)
RUN mkdir -p tmp \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 tmp assets config \
    && chmod -R 750 config

# Cron job for sentrion's background processing (every 10 minutes, per README)
RUN echo "*/10 * * * * www-data /usr/local/bin/php /var/www/html/index.php /cron >> /var/log/sentrion-cron.log 2>&1" > /etc/cron.d/sentrion-cron \
    && chmod 0644 /etc/cron.d/sentrion-cron \
    && crontab /etc/cron.d/sentrion-cron

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]
