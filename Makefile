.PHONY: help dev-up dev-down dev-build dev-logs dev-shell dev-migrate dev-fixtures dev-reset prod-up prod-down prod-build prod-logs prod-shell prod-migrate build-css

DOCKER_COMPOSE_DEV  = docker compose -f docker-compose.yml
DOCKER_COMPOSE_PROD = docker compose -f docker-compose.prod.yml --env-file .env.prod

help:
	@echo "Cabinet Athéna Dentaire — commandes Docker"
	@echo ""
	@echo "  make dev-up         Démarre l'environnement de développement"
	@echo "  make dev-down       Arrête l'environnement de développement"
	@echo "  make dev-build      Reconstruit les images de développement"
	@echo "  make dev-logs       Affiche les logs (dev)"
	@echo "  make dev-shell      Ouvre un shell dans le conteneur PHP (dev)"
	@echo "  make dev-migrate    Lance les migrations (dev)"
	@echo "  make dev-fixtures   Charge les fixtures (dev)"
	@echo "  make dev-reset      Réinitialise la base de dev + fixtures"
	@echo "  make build-css      Compile Tailwind CSS"
	@echo ""
	@echo "  make prod-up        Démarre l'environnement de production"
	@echo "  make prod-down      Arrête l'environnement de production"
	@echo "  make prod-build     Reconstruit les images de production"
	@echo "  make prod-logs      Affiche les logs (prod)"
	@echo "  make prod-shell     Ouvre un shell dans le conteneur PHP (prod)"
	@echo "  make prod-migrate   Lance les migrations (prod)"

build-css:
	npm run build:css

dev-up:
	@test -f .env.docker || cp .env.docker.example .env.docker
	$(DOCKER_COMPOSE_DEV) up -d --build

dev-down:
	$(DOCKER_COMPOSE_DEV) down

dev-build:
	$(DOCKER_COMPOSE_DEV) build --no-cache

dev-logs:
	$(DOCKER_COMPOSE_DEV) logs -f

dev-shell:
	$(DOCKER_COMPOSE_DEV) exec php sh

dev-migrate:
	$(DOCKER_COMPOSE_DEV) exec php php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

dev-fixtures:
	$(DOCKER_COMPOSE_DEV) exec php php bin/console doctrine:fixtures:load --no-interaction

dev-reset:
	$(DOCKER_COMPOSE_DEV) exec php php bin/console doctrine:schema:drop --force --full-database
	$(DOCKER_COMPOSE_DEV) exec php php bin/console doctrine:migrations:migrate --no-interaction
	$(DOCKER_COMPOSE_DEV) exec php php bin/console doctrine:fixtures:load --no-interaction

prod-up:
	@test -f .env.prod || (echo "Créez .env.prod à partir de .env.prod.example" && exit 1)
	$(DOCKER_COMPOSE_PROD) up -d --build

prod-down:
	$(DOCKER_COMPOSE_PROD) down

prod-build:
	$(DOCKER_COMPOSE_PROD) build --no-cache

prod-logs:
	$(DOCKER_COMPOSE_PROD) logs -f

prod-shell:
	$(DOCKER_COMPOSE_PROD) exec php sh

prod-migrate:
	$(DOCKER_COMPOSE_PROD) exec php php bin/console doctrine:migrations:migrate --no-interaction --env=prod
