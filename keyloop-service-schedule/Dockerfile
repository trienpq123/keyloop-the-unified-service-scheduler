FROM php:8.3-cli

ARG APP_USER=app
ARG APP_UID=1000

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        curl \
        unzip \
        libpq-dev \
        libzip-dev \
    && docker-php-ext-install \
        pdo_pgsql \
        zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN useradd \
    --uid ${APP_UID} \
    --create-home \
    --shell /bin/bash \
    ${APP_USER}

WORKDIR /var/www/html

COPY --chown=${APP_USER}:${APP_USER} . .

USER ${APP_USER}

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]