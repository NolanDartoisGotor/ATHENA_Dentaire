#!/bin/sh
set -e

cd /var/www/html

wait_for_database() {
    case "$DATABASE_URL" in
        postgresql:*|postgres:*)
            PGHOST="${POSTGRES_HOST:-database}"
            PGUSER="${POSTGRES_USER:-athena}"
            PGDATABASE="${POSTGRES_DB:-athena}"

            echo "Attente de PostgreSQL (${PGHOST})..."
            ATTEMPTS=0
            MAX_ATTEMPTS=30

            until pg_isready -h "$PGHOST" -U "$PGUSER" -d "$PGDATABASE" > /dev/null 2>&1; do
                ATTEMPTS=$((ATTEMPTS + 1))
                if [ "$ATTEMPTS" -ge "$MAX_ATTEMPTS" ]; then
                    echo "PostgreSQL indisponible après ${MAX_ATTEMPTS} tentatives."
                    exit 1
                fi
                sleep 2
            done
            ;;
        *)
            return 0
            ;;
    esac

    echo "Base de données disponible."
}

if [ "$APP_ENV" = "prod" ]; then
    wait_for_database

    if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
        php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration
    fi

    php bin/console cache:clear --env=prod --no-debug
    php bin/console cache:warmup --env=prod --no-debug
fi

if [ "$APP_ENV" = "dev" ] && [ "${DOCKER_AUTO_SETUP:-true}" = "true" ]; then
    if [ ! -d vendor ]; then
        composer install --no-interaction --prefer-dist
    fi

    if [ -f package.json ] && [ ! -d node_modules ]; then
        npm ci
        npm run build:css
    fi

    wait_for_database
    php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration || true
fi

exec docker-php-entrypoint "$@"
