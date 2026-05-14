DOCKER_COMPOSE_COMMAND=docker compose
WEBSERVER_SERVICE_NAME=inventario480-api

.PHONY: help dc-up dc-up-d dc-up-d-rebuild dc-down dc-exec dc-exec-root \
        composer composer-install composer-update start bash bin-console \
        prepare-dev-db prepare-test-db code-quality \
        tests-unit tests-functional tests-api tests-all \
        migrations-diff migrations-migrate migrations-status

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

# DOCKER COMPOSE
dc-up:               ARGS=up
dc-up-d:             ARGS=up -d
dc-up-d-rebuild:     ARGS=up -d --build --force-recreate
dc-down:             ARGS=down
dc-up dc-up-d dc-up-d-rebuild dc-down: ## Wrapper docker compose
	@$(DOCKER_COMPOSE_COMMAND) $(ARGS)

dc-exec: ## Exec cmd in api container as www-data. Usage: make dc-exec COMMAND="ls"
	@$(DOCKER_COMPOSE_COMMAND) exec -u www-data:www-data $(WEBSERVER_SERVICE_NAME) sh -c "$(COMMAND)"

dc-exec-root: ## Exec cmd in api container as root. Usage: make dc-exec-root COMMAND="ls"
	@$(DOCKER_COMPOSE_COMMAND) exec $(WEBSERVER_SERVICE_NAME) sh -c "$(COMMAND)"

# COMPOSER
composer:           ARGS=
composer-install:   ARGS=install
composer-update:    ARGS=update
composer composer-install composer-update: ## Composer wrapper. Usage: make composer ARGS="--version"
	@make dc-exec COMMAND="$(ENVS) composer $(ARGS)"
	@make dc-exec-root COMMAND="chown -R www-data:www-data vendor"

# APP
start: ## Prepare dev environment from scratch
	@UID=$$(id -u) GID=$$(id -g) make dc-up-d-rebuild
	@make composer ENVS="APP_ENV=dev" ARGS="install"
	@make bin-console ARGS="lexik:jwt:generate-keypair --skip-if-exists"
	@make prepare-dev-db
	@make bin-console ARGS="doctrine:fixtures:load --no-interaction"
	@echo ""
	@echo "Listo. App en http://localhost"

bash: ## Open shell in api container
	@make dc-exec COMMAND="bash"

bin-console: ## Run php bin/console. Usage: make bin-console ARGS="cache:clear"
	@make dc-exec COMMAND="php bin/console $(ARGS)"

prepare-dev-db: ## Create + migrate dev DB
	@make bin-console ARGS="--env=dev doctrine:database:create --if-not-exists"
	@make bin-console ARGS="--env=dev doctrine:migrations:migrate --no-interaction"

prepare-test-db: ## Create + migrate test DB
	@make bin-console ARGS="--env=test doctrine:database:create --if-not-exists"
	@make bin-console ARGS="--env=test doctrine:migrations:migrate --no-interaction"

# QUALITY
code-quality: ## Run phpcs + phpstan + deptrac
	@make composer ARGS="phpcs"
	@make composer ARGS="phpstan"
	@make composer ARGS="deptrac"

# TESTS
tests-unit: ## Run unit tests
	@make composer ARGS="tests:unit"

tests-functional: ## Run functional tests
	@make composer ENVS="COMPOSER_PROCESS_TIMEOUT=1800" ARGS="tests:functional"

tests-api: ## Run E2E API tests (Codeception)
	@make prepare-test-db
	@make composer ENVS="COMPOSER_PROCESS_TIMEOUT=1800" ARGS="tests:api"

tests-all: tests-unit tests-functional tests-api ## Run every test suite

# MIGRATIONS
migrations-diff: ## Generate migration diff
	@make bin-console ARGS="doctrine:migrations:diff"

migrations-migrate: ## Run pending migrations
	@make bin-console ARGS="doctrine:migrations:migrate --no-interaction"

migrations-status: ## Show migrations status
	@make bin-console ARGS="doctrine:migrations:status"
