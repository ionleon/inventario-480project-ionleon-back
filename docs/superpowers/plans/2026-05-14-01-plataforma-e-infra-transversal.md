# Plan 1 — Plataforma + Infraestructura Transversal (Gate A)

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Levantar runtime FrankenPHP + CI GitHub Actions + Codeception + cimientos de código (buses, base classes, exception listener inactivo) que desbloquean la migración por slices.

**Architecture:** Dos tracks que pueden ejecutarse en paralelo (track A = plataforma, track B = código transversal). El gate A al final verifica que ambos están en su sitio y la app vieja sigue arrancando.

**Tech Stack:** FrankenPHP 1.5 / PHP 8.3 alpine · Symfony 7 · Postgres 17 · Codeception 5 · Symfony Messenger · qossmic/deptrac-shim · GitHub Actions.

**Referencia obligatoria:** `docs/superpowers/specs/2026-05-14-arquitectura-ddd-cqrs-design.md` (sections §4 stack objetivo, §5 Fase 0 y Fase 1).

---

## Estructura de archivos creados/modificados

```
Dockerfile                                          # REEMPLAZADO
compose.yaml                                        # NUEVO (sustituye docker-compose.yml)
.dockerignore                                       # NUEVO
.docker/
  caddy/{classic.caddyfile, worker.caddyfile}       # NUEVO
  php/conf.d/{zz-php.ini, dev/, prod/}              # NUEVO
  postgres/{Dockerfile, init/.gitkeep}              # NUEVO
Makefile                                            # NUEVO
codeception.yml                                     # NUEVO
deptrac.yaml                                        # NUEVO
.env.test.pipeline                                  # NUEVO
.github/workflows/ci.yml                            # NUEVO
phpunit.dist.xml                                    # MODIFICADO
composer.json                                       # MODIFICADO (require-dev)
readme.md                                           # REESCRITO
docker-compose.yml                                  # BORRADO
deploy.sh                                           # BORRADO

config/packages/messenger.yaml                      # MODIFICADO
config/packages/doctrine.yaml                       # MODIFICADO
config/services.yaml                                # MODIFICADO

src/Shared/Domain/Model/CustomException.php         # NUEVO
src/Shared/Domain/Model/ErrorCode.php               # NUEVO
src/Core/Domain/Model/AggregateRoot.php             # NUEVO
src/Core/Application/Bus/{Command,Query,CommandHandler,QueryHandler,CommandBus,QueryBus}.php  # NUEVO
src/Core/Application/DTO/Security/SecurityToken.php # NUEVO
src/Core/Application/Command/Common/Security/{SecurableHandler.php, SecurityAwareTrait.php}   # NUEVO
src/Core/Domain/Service/Security/{SecurityChecker.php, PermissiveSecurityChecker.php}         # NUEVO
src/Core/Domain/Exception/Security/ForbiddenException.php  # NUEVO
src/App/Auth/Domain/Service/SecurityTokenExtractorInterface.php          # NUEVO
src/App/Auth/Infrastructure/JwtSecurityTokenExtractor.php                # NUEVO
src/App/UI/API/Response/Model/JsonContentErrorResponse.php               # NUEVO
src/App/UI/API/Response/Service/{ExceptionListener.php, MapperExceptionToJsonErrorResponse.php, GetCurrentEnvironment.php}  # NUEVO

tests/Support/ApiTester.php                         # NUEVO (skeleton)
tests/Unit/Shared/Domain/Model/ErrorCodeTest.php    # NUEVO
tests/Unit/Core/Domain/Model/AggregateRootTest.php  # NUEVO
tests/Unit/Core/Application/Bus/BusContractTest.php # NUEVO
tests/Unit/App/Auth/Infrastructure/JwtSecurityTokenExtractorTest.php  # NUEVO
```

---

## TRACK A — Plataforma

### Task 1: Dockerfile multi-stage FrankenPHP

**Files:** Create `Dockerfile`

- [ ] **Step 1: Borrar `Dockerfile` actual** (PHP-FPM)

```bash
rm Dockerfile
```

- [ ] **Step 2: Crear `Dockerfile` multi-stage**

```dockerfile
# syntax=docker/dockerfile:1
FROM dunglas/frankenphp:1.5.0-php8.3-alpine AS base

ARG APP_USER_ID=1000
ARG APP_GROUP_ID=1000
ARG FRANKENPHP_WORKER_MODE_ENABLED=false

ENV FRANKENPHP_WORKER_MODE_ENABLED=${FRANKENPHP_WORKER_MODE_ENABLED}

RUN apk add --no-cache git unzip bash icu-libs su-exec \
 && install-php-extensions @composer pdo_pgsql intl opcache zip

# Sync user/group with host to avoid permission issues on bind mounts
RUN if [ "$(id -u www-data)" != "$APP_USER_ID" ]; then \
      deluser www-data || true; \
      addgroup -g $APP_GROUP_ID -S www-data || true; \
      adduser -u $APP_USER_ID -G www-data -S www-data; \
    fi

WORKDIR /app

# Choose Caddyfile depending on FRANKENPHP_WORKER_MODE_ENABLED at container start
COPY .docker/caddy/classic.caddyfile /etc/caddy/classic.Caddyfile
COPY .docker/caddy/worker.caddyfile  /etc/caddy/worker.Caddyfile

# ---------- DEV ----------
FROM base AS dev

RUN install-php-extensions xdebug
COPY .docker/php/conf.d/zz-php.ini /usr/local/etc/php/conf.d/zz-php.ini
COPY .docker/php/conf.d/dev/       /usr/local/etc/php/conf.d/

ENV APP_ENV=dev
CMD ["sh", "-c", "if [ \"$FRANKENPHP_WORKER_MODE_ENABLED\" = \"true\" ]; then frankenphp run --config /etc/caddy/worker.Caddyfile; else frankenphp run --config /etc/caddy/classic.Caddyfile; fi"]

# ---------- PROD ----------
FROM base AS prod

COPY .docker/php/conf.d/zz-php.ini /usr/local/etc/php/conf.d/zz-php.ini
COPY .docker/php/conf.d/prod/      /usr/local/etc/php/conf.d/

COPY --chown=www-data:www-data . /app
USER www-data
RUN composer install --no-dev --no-progress --optimize-autoloader

ENV APP_ENV=prod
CMD ["frankenphp", "run", "--config", "/etc/caddy/classic.Caddyfile"]
```

- [ ] **Step 3: Commit**

```bash
git add Dockerfile
git commit -m "chore(docker): replace PHP-FPM Dockerfile with FrankenPHP multi-stage"
```

---

### Task 2: `.dockerignore`

**Files:** Create `.dockerignore`

- [ ] **Step 1: Crear `.dockerignore`**

```
.gitignore
.dockerignore
.idea
.git
vendor
var
node_modules
tests
docs
arquitectura-general
```

- [ ] **Step 2: Commit**

```bash
git add .dockerignore
git commit -m "chore(docker): add .dockerignore"
```

---

### Task 3: Configs de Caddy (classic + worker)

**Files:** Create `.docker/caddy/{classic.caddyfile, worker.caddyfile}`

- [ ] **Step 1: Crear `.docker/caddy/classic.caddyfile`**

```caddyfile
{
    {$CADDY_GLOBAL_OPTIONS}

    frankenphp {
        {$FRANKENPHP_CONFIG}
    }
}

{$CADDY_EXTRA_CONFIG}

{$SERVER_NAME:localhost} {
    log {
        level WARN
        {$CADDY_SERVER_LOG_OPTIONS}
    }

    root * /app/public/
    encode zstd br gzip

    {$CADDY_SERVER_EXTRA_DIRECTIVES}

    @phpRoute {
        not file {path}
    }

    rewrite @phpRoute index.php

    @frontController path index.php
    php @frontController

    file_server {
        hide *.php
    }
}
```

- [ ] **Step 2: Crear `.docker/caddy/worker.caddyfile`**

```caddyfile
{
    {$CADDY_GLOBAL_OPTIONS}

    frankenphp {
        {$FRANKENPHP_CONFIG}

        worker {
            file ./public/index.php
            env APP_RUNTIME Runtime\FrankenPhpSymfony\Runtime
            {$FRANKENPHP_WORKER_CONFIG}
        }
    }
}

{$CADDY_EXTRA_CONFIG}

{$SERVER_NAME:localhost} {
    log {
        level WARN
        {$CADDY_SERVER_LOG_OPTIONS}
    }

    root /app/public
    encode zstd br gzip

    {$CADDY_SERVER_EXTRA_DIRECTIVES}

    @phpRoute {
        not file {path}
    }

    rewrite @phpRoute index.php

    @frontController path index.php
    php @frontController

    file_server {
        hide *.php
    }
}
```

- [ ] **Step 3: Commit**

```bash
git add .docker/caddy
git commit -m "chore(docker): add Caddy configs (classic + worker mode)"
```

---

### Task 4: Configs de PHP

**Files:** Create `.docker/php/conf.d/{zz-php.ini, dev/zz-php-extend.ini, dev/xdebug.ini, prod/zz-php-extend.ini}`

- [ ] **Step 1: Crear `.docker/php/conf.d/zz-php.ini`**

```ini
[core]
max_execution_time = 30
max_input_time = 30
memory_limit = 512M
post_max_size = 256M
upload_max_filesize = 256M
```

- [ ] **Step 2: Crear `.docker/php/conf.d/dev/zz-php-extend.ini`**

```ini
[opcache]
opcache.enable=1
opcache.validate_timestamps=1
opcache.revalidate_freq=0

display_errors = On
display_startup_errors = On
error_reporting = E_ALL
```

- [ ] **Step 3: Crear `.docker/php/conf.d/dev/xdebug.ini`**

```ini
[xdebug]
xdebug.mode=develop,debug
xdebug.client_host=host.docker.internal
xdebug.client_port=9003
xdebug.start_with_request=trigger
```

- [ ] **Step 4: Crear `.docker/php/conf.d/prod/zz-php-extend.ini`**

```ini
[opcache]
opcache.enable=1
opcache.validate_timestamps=0
opcache.preload=/app/config/preload.php
opcache.preload_user=www-data
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=20000
```

- [ ] **Step 5: Commit**

```bash
git add .docker/php
git commit -m "chore(docker): add PHP configs for dev and prod"
```

---

### Task 5: Postgres Dockerfile

**Files:** Create `.docker/postgres/{Dockerfile, init/.gitkeep}`

- [ ] **Step 1: Crear `.docker/postgres/Dockerfile`**

```dockerfile
FROM postgres:17-alpine

# Placeholder for future init scripts in /docker-entrypoint-initdb.d
```

- [ ] **Step 2: Crear `.docker/postgres/init/.gitkeep`**

```bash
mkdir -p .docker/postgres/init
touch .docker/postgres/init/.gitkeep
```

- [ ] **Step 3: Commit**

```bash
git add .docker/postgres
git commit -m "chore(docker): add Postgres 17 Dockerfile"
```

---

### Task 6: `compose.yaml` con dos Postgres

**Files:** Create `compose.yaml`, delete `docker-compose.yml`

- [ ] **Step 1: Borrar `docker-compose.yml` actual**

```bash
git rm docker-compose.yml
```

- [ ] **Step 2: Crear `compose.yaml`**

```yaml
services:
  inventario480-api:
    working_dir: /app
    build:
      dockerfile: Dockerfile
      context: .
      target: dev
      args:
        APP_USER_ID: ${UID:-1000}
        APP_GROUP_ID: ${GID:-1000}
        FRANKENPHP_WORKER_MODE_ENABLED: ${FRANKENPHP_WORKER_MODE_ENABLED:-false}
    restart: unless-stopped
    volumes:
      - ./:/app
      - caddy_data:/data
      - caddy_config:/config
    ports:
      - "80:80"
      - "443:443"
      - "443:443/udp"
    depends_on:
      inventario480-db:
        condition: service_healthy
    extra_hosts:
      - host.docker.internal:host-gateway

  inventario480-db:
    build:
      context: ./.docker/postgres
      dockerfile: Dockerfile
    restart: unless-stopped
    environment:
      POSTGRES_DB: project_inventory_480_db
      POSTGRES_USER: user_admin
      POSTGRES_PASSWORD: skibidiman123
      TZ: "Europe/Madrid"
    ports:
      - "5432:5432"
    volumes:
      - inventario480_db_data:/var/lib/postgresql/data
      - ./.docker/postgres/init:/docker-entrypoint-initdb.d
    healthcheck:
      test: ["CMD-SHELL", "pg_isready -U user_admin -d project_inventory_480_db"]
      interval: 10s
      timeout: 5s
      retries: 5

  inventario480-db-test:
    build:
      context: ./.docker/postgres
      dockerfile: Dockerfile
    restart: unless-stopped
    environment:
      POSTGRES_DB: project_inventory_480_db_test
      POSTGRES_USER: user_admin
      POSTGRES_PASSWORD: skibidiman123
      TZ: "Europe/Madrid"
      PGPORT: 5433
    ports:
      - "5433:5433"
    volumes:
      - ./.docker/postgres/init:/docker-entrypoint-initdb.d
    healthcheck:
      test: ["CMD-SHELL", "pg_isready -U user_admin -d project_inventory_480_db_test -p 5433"]
      interval: 10s
      timeout: 5s
      retries: 5

volumes:
  inventario480_db_data:
  caddy_data:
  caddy_config:
```

- [ ] **Step 3: Commit**

```bash
git add compose.yaml docker-compose.yml
git commit -m "chore(docker): switch to compose.yaml with dev + test Postgres"
```

---

### Task 7: Borrar `deploy.sh` (su contenido pasa al Makefile)

**Files:** Delete `deploy.sh`

- [ ] **Step 1: Borrar**

```bash
git rm deploy.sh
```

- [ ] **Step 2: Commit** (lo combinaremos con el Makefile en el siguiente task)

---

### Task 8: Crear Makefile

**Files:** Create `Makefile`

- [ ] **Step 1: Crear `Makefile`**

```makefile
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
```

- [ ] **Step 2: Commit**

```bash
git add Makefile deploy.sh
git commit -m "chore: add Makefile, remove deploy.sh"
```

---

### Task 9: Reescribir README

**Files:** Modify `readme.md`

- [ ] **Step 1: Sobrescribir `readme.md`**

```markdown
# Inventario 480 Project — Backend

API REST en Symfony 7.3 + PHP 8.3 sobre FrankenPHP, PostgreSQL 17 y JWT (Lexik).

## Requisitos

- Docker (con plugin compose v2)
- Make
- (Opcional) Cuenta GitHub SSH configurada para el remote

## Arranque

```bash
make start
```

Esto construye los contenedores, instala dependencias, genera el par de claves JWT, crea la BD, ejecuta migraciones y carga fixtures.

App disponible en `http://localhost`.

## Comandos habituales

```bash
make bash                # shell en el contenedor api
make bin-console ARGS="cache:clear"
make migrations-migrate
make code-quality        # phpcs + phpstan + deptrac
make tests-unit
make tests-api           # E2E Codeception (recrea DB de tests)
make tests-all
```

Ver `make help` para la lista completa.

## Arquitectura

El proyecto sigue **DDD por capas + CQRS** según `arquitectura-general/`. Para añadir una feature, lee `arquitectura-general/04-anadir-una-feature.md`. El estado del refactor activo está documentado en `docs/superpowers/specs/` y `docs/superpowers/plans/`.

## Autenticación

```bash
curl -X POST http://localhost/480project/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password1234"}'
```
```

- [ ] **Step 2: Commit**

```bash
git add readme.md
git commit -m "docs: rewrite README for FrankenPHP + Make workflow"
```

---

### Task 10: Instalar Codeception y configurar suites

**Files:** Modify `composer.json`, create `codeception.yml`, create `tests/{Unit,Functional,Api}/`

- [ ] **Step 1: Instalar dependencias**

```bash
make composer ARGS="require --dev codeception/codeception codeception/module-symfony codeception/module-db codeception/module-rest codeception/module-asserts"
```

- [ ] **Step 2: Crear `codeception.yml`**

```yaml
namespace: App\Tests
paths:
    tests: tests
    output: var/log/codeception
    data: tests/_data
    support: tests/Support
    envs: tests/_envs
actor_suffix: Tester
extensions:
    enabled:
        - Codeception\Extension\RunFailed
```

- [ ] **Step 3: Crear suite Unit**

`tests/Unit.suite.yml`:

```yaml
actor: UnitTester
suite_namespace: App\Tests\Unit
modules:
    enabled:
        - Asserts
        - \App\Tests\Support\Helper\Unit
```

- [ ] **Step 4: Crear suite Functional**

`tests/Functional.suite.yml`:

```yaml
actor: FunctionalTester
suite_namespace: App\Tests\Functional
modules:
    enabled:
        - Symfony:
            app_path: 'src'
            environment: 'test'
        - Doctrine:
            depends: Symfony
            cleanup: true
        - \App\Tests\Support\Helper\Functional
```

- [ ] **Step 5: Crear suite Api**

`tests/Api.suite.yml`:

```yaml
actor: ApiTester
suite_namespace: App\Tests\Api
modules:
    enabled:
        - REST:
            url: http://localhost
            depends: Symfony
        - Symfony:
            app_path: 'src'
            environment: 'test'
        - Doctrine:
            depends: Symfony
            cleanup: true
        - \App\Tests\Support\Helper\Api
```

- [ ] **Step 6: Crear esqueleto `tests/Support/ApiTester.php`**

```php
<?php

declare(strict_types=1);

namespace App\Tests\Support;

use Codeception\Actor;

/**
 * @SuppressWarnings(PHPMD)
 */
class ApiTester extends Actor
{
    use _generated\ApiTesterActions;

    public function haveAdminHttpHeaders(string $language = 'es'): void
    {
        $this->haveHttpHeader('Content-Type', 'application/json');
        $this->haveHttpHeader('Api-Language', $language);
        // JWT generation helper added later in slice plans
    }

    public function seeResponseErrorCodeContent(string $expectedCode): void
    {
        $this->seeResponseContainsJson(['code' => $expectedCode]);
    }
}
```

- [ ] **Step 7: Helpers placeholders**

```bash
mkdir -p tests/Support/Helper
```

Crear `tests/Support/Helper/Unit.php`, `tests/Support/Helper/Functional.php`, `tests/Support/Helper/Api.php` cada uno con:

```php
<?php

declare(strict_types=1);

namespace App\Tests\Support\Helper;

use Codeception\Module;

class Unit extends Module
{
}
```

(Repetir cambiando `Unit` por `Functional` y `Api` en sus respectivos archivos.)

- [ ] **Step 8: Adaptar `phpunit.dist.xml`**

Sustituir el testsuite actual por:

```xml
<testsuites>
    <testsuite name="Unit">
        <directory>tests/Unit</directory>
    </testsuite>
</testsuites>
```

- [ ] **Step 9: Añadir scripts a `composer.json`**

En la sección `scripts`:

```json
"phpcs": "phpcs",
"phpstan": "phpstan analyse",
"deptrac": "deptrac analyse",
"tests:unit": "codecept run Unit --steps",
"tests:functional": "codecept run Functional --steps",
"tests:api": "codecept run Api --steps",
"tests:all": ["@tests:unit", "@tests:functional", "@tests:api"]
```

- [ ] **Step 10: Ejecutar `composer dump-autoload`**

```bash
make composer ARGS="dump-autoload"
```

- [ ] **Step 11: Verificar `make tests-unit` ejecuta**

```bash
make tests-unit
```

Expected: "No tests found" o suite vacía sin error.

- [ ] **Step 12: Commit**

```bash
git add composer.json composer.lock codeception.yml tests phpunit.dist.xml
git commit -m "test: bootstrap Codeception suites (Unit, Functional, Api)"
```

---

### Task 11: `.env.test.pipeline` para CI

**Files:** Create `.env.test.pipeline`

- [ ] **Step 1: Crear `.env.test.pipeline`**

```env
APP_ENV=test
APP_SECRET=ci_secret_key_for_pipelines_only_change_if_needed
DATABASE_URL="postgresql://user_admin:skibidiman123@localhost:5433/project_inventory_480_db_test?serverVersion=17&charset=utf8"
JWT_PASSPHRASE=ci_jwt_passphrase_for_pipelines
CORS_ALLOW_ORIGIN='^https?://(localhost|127\.0\.0\.1)(:[0-9]+)?$'
```

- [ ] **Step 2: Commit**

```bash
git add .env.test.pipeline
git commit -m "test: add .env.test.pipeline for CI runner"
```

---

### Task 12: Workflow GitHub Actions

**Files:** Create `.github/workflows/ci.yml`

- [ ] **Step 1: Crear `.github/workflows/ci.yml`**

```yaml
name: CI

on:
  push:
    branches: ['**']
  pull_request:

jobs:
  composer-install:
    runs-on: ubuntu-latest
    container:
      image: dunglas/frankenphp:1.5.0-php8.3-alpine
    steps:
      - uses: actions/checkout@v4
      - name: Install PHP extensions
        run: install-php-extensions @composer pdo_pgsql intl opcache zip
      - name: Cache composer
        uses: actions/cache@v4
        with:
          path: vendor
          key: composer-${{ hashFiles('composer.lock') }}
      - name: Install
        run: composer install --no-progress --no-interaction
      - uses: actions/upload-artifact@v4
        with:
          name: vendor
          path: vendor

  lint:
    needs: composer-install
    runs-on: ubuntu-latest
    container:
      image: dunglas/frankenphp:1.5.0-php8.3-alpine
    steps:
      - uses: actions/checkout@v4
      - uses: actions/download-artifact@v4
        with: { name: vendor, path: vendor }
      - run: composer phpcs

  phpstan:
    needs: composer-install
    runs-on: ubuntu-latest
    container:
      image: dunglas/frankenphp:1.5.0-php8.3-alpine
    steps:
      - uses: actions/checkout@v4
      - uses: actions/download-artifact@v4
        with: { name: vendor, path: vendor }
      - run: composer phpstan

  deptrac:
    needs: composer-install
    runs-on: ubuntu-latest
    container:
      image: dunglas/frankenphp:1.5.0-php8.3-alpine
    steps:
      - uses: actions/checkout@v4
      - uses: actions/download-artifact@v4
        with: { name: vendor, path: vendor }
      - run: composer deptrac

  tests-unit:
    needs: composer-install
    runs-on: ubuntu-latest
    container:
      image: dunglas/frankenphp:1.5.0-php8.3-alpine
    steps:
      - uses: actions/checkout@v4
      - uses: actions/download-artifact@v4
        with: { name: vendor, path: vendor }
      - run: install-php-extensions pdo_pgsql intl
      - run: cp .env.test.pipeline .env.test
      - run: composer dump-env test
      - run: composer tests:unit

  tests-api:
    needs: composer-install
    runs-on: ubuntu-latest
    container:
      image: dunglas/frankenphp:1.5.0-php8.3-alpine
    services:
      postgres:
        image: postgres:17-alpine
        ports: ['5433:5432']
        env:
          POSTGRES_DB: project_inventory_480_db_test
          POSTGRES_USER: user_admin
          POSTGRES_PASSWORD: skibidiman123
        options: >-
          --health-cmd "pg_isready -U user_admin -d project_inventory_480_db_test"
          --health-interval 10s
          --health-timeout 5s
          --health-retries 5
    steps:
      - uses: actions/checkout@v4
      - uses: actions/download-artifact@v4
        with: { name: vendor, path: vendor }
      - run: install-php-extensions pdo_pgsql intl
      - run: echo "127.0.0.1 postgres" >> /etc/hosts
      - run: cp .env.test.pipeline .env.test
      - run: sed -i 's/localhost:5433/postgres:5432/' .env.test
      - run: composer dump-env test
      - name: Generate JWT keys
        run: php bin/console lexik:jwt:generate-keypair --skip-if-exists --env=test
      - name: Migrate
        run: php bin/console doctrine:migrations:migrate --no-interaction --env=test
      - name: Run FrankenPHP in worker mode
        run: frankenphp run --config .docker/caddy/worker.caddyfile &
      - name: Wait for server
        run: until curl -s http://localhost/ > /dev/null; do sleep 1; done
      - run: composer tests:api
```

- [ ] **Step 2: Commit**

```bash
git add .github
git commit -m "ci: add GitHub Actions workflow"
```

---

### Task 13: Verificación end-to-end del track A

- [ ] **Step 1: Levantar todo de cero**

```bash
make start
```

Expected: contenedores arriba, fixtures cargadas, app responde en `http://localhost`.

- [ ] **Step 2: Verificar endpoint de login del código viejo sigue funcionando**

```bash
curl -X POST http://localhost/480project/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password1234"}'
```

Expected: respuesta 200 con `token` JWT.

- [ ] **Step 3: Verificar tests-unit pasan (aunque vacíos)**

```bash
make tests-unit
```

Expected: 0 tests, 0 failures.

- [ ] **Step 4: Push y verificar CI**

```bash
git push origin feature/ddd-refactor
```

Expected: workflow GHA verde en https://github.com/ionleon/inventario-480project-ionleon-back/actions

---

## TRACK B — Infraestructura transversal del código

> Track B puede ejecutarse en paralelo a Track A. Si se hacen secuenciales, ejecutar Track A primero.

### Task 14: Instalar deptrac

**Files:** Modify `composer.json`, create `deptrac.yaml`

- [ ] **Step 1: Instalar deptrac**

```bash
make composer ARGS="require --dev qossmic/deptrac-shim"
```

- [ ] **Step 2: Crear `deptrac.yaml`**

```yaml
deptrac:
  paths:
    - ./src
  exclude_files: []
  layers:
    - name: Domain
      collectors:
        - { type: classLike, value: '.*\\Domain\\.*' }
    - name: Application
      collectors:
        - { type: classLike, value: '.*\\Application\\.*' }
    - name: Infrastructure
      collectors:
        - { type: classLike, value: '.*\\Infrastructure\\.*' }
    - name: App
      collectors:
        - { type: classLike, value: 'App\\App\\.*' }
    - name: Shared
      collectors:
        - { type: classLike, value: 'App\\Shared\\.*' }
  ruleset:
    Domain: ~
    Application:
      - Domain
    Infrastructure:
      - Domain
      - Application
    App:
      - Domain
      - Application
      - Infrastructure
      - Shared
    Shared: ~
```

- [ ] **Step 3: Ejecutar deptrac y aceptar baseline si hay infracciones del código viejo**

```bash
make composer ARGS="deptrac analyse --report-skipped"
```

Si el código viejo (UserManagement, etc.) viola las reglas, generar baseline:

```bash
make composer ARGS="deptrac analyse --formatter=baseline"
```

(Esto crea `deptrac.baseline.yaml` que se respeta hasta limpiar las violaciones legacy.)

- [ ] **Step 4: Commit**

```bash
git add composer.json composer.lock deptrac.yaml deptrac.baseline.yaml
git commit -m "chore: add deptrac with baseline for legacy violations"
```

---

### Task 15: `CustomException` y `ErrorCode` enum

**Files:** Create `src/Shared/Domain/Model/{CustomException.php, ErrorCode.php}`, create `tests/Unit/Shared/Domain/Model/ErrorCodeTest.php`

- [ ] **Step 1: Crear test del enum**

`tests/Unit/Shared/Domain/Model/ErrorCodeTest.php`:

```php
<?php

declare(strict_types=1);

namespace App\Tests\Unit\Shared\Domain\Model;

use App\Shared\Domain\Model\ErrorCode;
use PHPUnit\Framework\TestCase;

final class ErrorCodeTest extends TestCase
{
    public function test_GivenErrorCode_WhenAccessingCases_ThenAllRequiredCodesExist(): void
    {
        $required = [
            'BAD_REQUEST', 'FORBIDDEN', 'NOT_FOUND', 'CONFLICT',
            'UNEXPECTED_ERROR', 'INVALID_UUID', 'PAYLOAD_VALIDATION_FAILED',
            'INVALID_USER_ID', 'USER_NOT_FOUND', 'DUPLICATED_USER_EMAIL',
            'INVALID_CLIENT_ID', 'CLIENT_NOT_FOUND', 'DUPLICATED_CLIENT_NAME',
            'INVALID_PROJECT_ID', 'PROJECT_NOT_FOUND',
        ];

        $existing = array_map(fn(ErrorCode $c) => $c->value, ErrorCode::cases());

        foreach ($required as $code) {
            self::assertContains($code, $existing, "ErrorCode::$code is missing");
        }
    }
}
```

- [ ] **Step 2: Ejecutar test y verificar que falla**

```bash
make tests-unit
```

Expected: FAIL "Class ErrorCode not found".

- [ ] **Step 3: Crear `src/Shared/Domain/Model/ErrorCode.php`** (pre-rellenado para los 11 aggregates)

```php
<?php

declare(strict_types=1);

namespace App\Shared\Domain\Model;

enum ErrorCode: string
{
    // HTTP / comunes
    case BAD_REQUEST = 'BAD_REQUEST';
    case FORBIDDEN = 'FORBIDDEN';
    case NOT_FOUND = 'NOT_FOUND';
    case CONFLICT = 'CONFLICT';
    case UNEXPECTED_ERROR = 'UNEXPECTED_ERROR';
    case INVALID_UUID = 'INVALID_UUID';
    case PAYLOAD_VALIDATION_FAILED = 'PAYLOAD_VALIDATION_FAILED';
    case INVALID_PAYLOAD = 'INVALID_PAYLOAD';

    // VO comunes
    case INVALID_EMAIL = 'INVALID_EMAIL';
    case INVALID_PASSWORD = 'INVALID_PASSWORD';
    case INVALID_PHONE = 'INVALID_PHONE';
    case INVALID_URL = 'INVALID_URL';

    // USER
    case INVALID_USER_ID = 'INVALID_USER_ID';
    case USER_NOT_FOUND = 'USER_NOT_FOUND';
    case DUPLICATED_USER_EMAIL = 'DUPLICATED_USER_EMAIL';
    case INVALID_USER_NAME = 'INVALID_USER_NAME';
    case INVALID_USER_SURNAME = 'INVALID_USER_SURNAME';

    // SECTOR
    case INVALID_SECTOR_ID = 'INVALID_SECTOR_ID';
    case SECTOR_NOT_FOUND = 'SECTOR_NOT_FOUND';
    case DUPLICATED_SECTOR_NAME = 'DUPLICATED_SECTOR_NAME';
    case INVALID_SECTOR_NAME = 'INVALID_SECTOR_NAME';

    // TECHNOLOGY
    case INVALID_TECHNOLOGY_ID = 'INVALID_TECHNOLOGY_ID';
    case TECHNOLOGY_NOT_FOUND = 'TECHNOLOGY_NOT_FOUND';
    case DUPLICATED_TECHNOLOGY_NAME = 'DUPLICATED_TECHNOLOGY_NAME';
    case INVALID_TECHNOLOGY_NAME = 'INVALID_TECHNOLOGY_NAME';

    // PROJECT_ROLE
    case INVALID_PROJECT_ROLE_ID = 'INVALID_PROJECT_ROLE_ID';
    case PROJECT_ROLE_NOT_FOUND = 'PROJECT_ROLE_NOT_FOUND';
    case DUPLICATED_PROJECT_ROLE_NAME = 'DUPLICATED_PROJECT_ROLE_NAME';

    // REFRESH_TOKEN
    case INVALID_REFRESH_TOKEN_ID = 'INVALID_REFRESH_TOKEN_ID';
    case REFRESH_TOKEN_NOT_FOUND = 'REFRESH_TOKEN_NOT_FOUND';
    case REFRESH_TOKEN_EXPIRED = 'REFRESH_TOKEN_EXPIRED';
    case REFRESH_TOKEN_REVOKED = 'REFRESH_TOKEN_REVOKED';

    // CLIENT
    case INVALID_CLIENT_ID = 'INVALID_CLIENT_ID';
    case CLIENT_NOT_FOUND = 'CLIENT_NOT_FOUND';
    case DUPLICATED_CLIENT_NAME = 'DUPLICATED_CLIENT_NAME';
    case INVALID_CLIENT_NAME = 'INVALID_CLIENT_NAME';

    // CONTACT
    case INVALID_CONTACT_ID = 'INVALID_CONTACT_ID';
    case CONTACT_NOT_FOUND = 'CONTACT_NOT_FOUND';
    case INVALID_CONTACT_NAME = 'INVALID_CONTACT_NAME';

    // LINK
    case INVALID_LINK_ID = 'INVALID_LINK_ID';
    case LINK_NOT_FOUND = 'LINK_NOT_FOUND';
    case INVALID_LINK_URL = 'INVALID_LINK_URL';

    // PROJECT
    case INVALID_PROJECT_ID = 'INVALID_PROJECT_ID';
    case PROJECT_NOT_FOUND = 'PROJECT_NOT_FOUND';
    case DUPLICATED_PROJECT_NAME = 'DUPLICATED_PROJECT_NAME';
    case INVALID_PROJECT_NAME = 'INVALID_PROJECT_NAME';
    case INVALID_PROJECT_DATE_RANGE = 'INVALID_PROJECT_DATE_RANGE';

    // PROJECT_USER
    case INVALID_PROJECT_USER_ID = 'INVALID_PROJECT_USER_ID';
    case PROJECT_USER_NOT_FOUND = 'PROJECT_USER_NOT_FOUND';
    case DUPLICATED_PROJECT_USER = 'DUPLICATED_PROJECT_USER';
    case INVALID_PROJECT_USER_ALLOCATION = 'INVALID_PROJECT_USER_ALLOCATION';

    // TIME_ENTRY
    case INVALID_TIME_ENTRY_ID = 'INVALID_TIME_ENTRY_ID';
    case TIME_ENTRY_NOT_FOUND = 'TIME_ENTRY_NOT_FOUND';
    case INVALID_TIME_ENTRY_HOURS = 'INVALID_TIME_ENTRY_HOURS';
    case INVALID_TIME_ENTRY_DATE = 'INVALID_TIME_ENTRY_DATE';
}
```

- [ ] **Step 4: Crear `src/Shared/Domain/Model/CustomException.php`**

```php
<?php

declare(strict_types=1);

namespace App\Shared\Domain\Model;

use Exception;
use Throwable;

class CustomException extends Exception
{
    /** @param array<string,string> $messageParams */
    public function __construct(
        string $message,
        public readonly ErrorCode $errorCode,
        public readonly array $messageParams = [],
        ?Throwable $previous = null,
    ) {
        parent::__construct(message: $message, previous: $previous);
    }
}
```

- [ ] **Step 5: Ejecutar tests y verificar que pasan**

```bash
make tests-unit
```

Expected: PASS.

- [ ] **Step 6: Commit**

```bash
git add src/Shared tests/Unit/Shared
git commit -m "feat(shared): add CustomException + pre-filled ErrorCode enum"
```

---

### Task 16: `AggregateRoot` base con eventos

**Files:** Create `src/Core/Domain/Model/AggregateRoot.php`, create `tests/Unit/Core/Domain/Model/AggregateRootTest.php`

- [ ] **Step 1: Crear test**

`tests/Unit/Core/Domain/Model/AggregateRootTest.php`:

```php
<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Model;

use App\Core\Domain\Model\AggregateRoot;
use PHPUnit\Framework\TestCase;

final class AggregateRootTest extends TestCase
{
    public function test_GivenAggregate_WhenRecordEvent_ThenPullEventsReturnsAndClears(): void
    {
        $aggregate = new class extends AggregateRoot {
            public function fire(object $event): void { $this->recordEvent($event); }
        };

        $event1 = new \stdClass();
        $event2 = new \stdClass();

        $aggregate->fire($event1);
        $aggregate->fire($event2);

        $pulled = $aggregate->pullEvents();

        self::assertSame([$event1, $event2], $pulled);
        self::assertSame([], $aggregate->pullEvents());
    }
}
```

- [ ] **Step 2: Run test → FAIL** "AggregateRoot not found".

```bash
make tests-unit
```

- [ ] **Step 3: Crear `src/Core/Domain/Model/AggregateRoot.php`**

```php
<?php

declare(strict_types=1);

namespace App\Core\Domain\Model;

abstract class AggregateRoot
{
    /** @var list<object> */
    private array $pendingEvents = [];

    protected function recordEvent(object $event): void
    {
        $this->pendingEvents[] = $event;
    }

    /** @return list<object> */
    public function pullEvents(): array
    {
        $events = $this->pendingEvents;
        $this->pendingEvents = [];
        return $events;
    }
}
```

- [ ] **Step 4: Run test → PASS**

- [ ] **Step 5: Commit**

```bash
git add src/Core/Domain/Model/AggregateRoot.php tests/Unit/Core/Domain/Model
git commit -m "feat(core): add AggregateRoot base with event recording"
```

---

### Task 17: Marker interfaces del bus

**Files:** Create `src/Core/Application/Bus/{Command,Query,CommandHandler,QueryHandler,CommandBus,QueryBus}.php`

- [ ] **Step 1: Crear los 6 archivos**

`src/Core/Application/Bus/Command.php`:

```php
<?php
declare(strict_types=1);
namespace App\Core\Application\Bus;
interface Command {}
```

`src/Core/Application/Bus/Query.php`:

```php
<?php
declare(strict_types=1);
namespace App\Core\Application\Bus;
interface Query {}
```

`src/Core/Application/Bus/CommandHandler.php`:

```php
<?php
declare(strict_types=1);
namespace App\Core\Application\Bus;
interface CommandHandler {}
```

`src/Core/Application/Bus/QueryHandler.php`:

```php
<?php
declare(strict_types=1);
namespace App\Core\Application\Bus;
interface QueryHandler {}
```

`src/Core/Application/Bus/CommandBus.php`:

```php
<?php
declare(strict_types=1);
namespace App\Core\Application\Bus;
interface CommandBus
{
    public function handle(Command $command): void;
}
```

`src/Core/Application/Bus/QueryBus.php`:

```php
<?php
declare(strict_types=1);
namespace App\Core\Application\Bus;
interface QueryBus
{
    public function ask(Query $query): mixed;
}
```

- [ ] **Step 2: Crear test de contrato**

`tests/Unit/Core/Application/Bus/BusContractTest.php`:

```php
<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Application\Bus;

use App\Core\Application\Bus\Command;
use App\Core\Application\Bus\CommandBus;
use App\Core\Application\Bus\CommandHandler;
use App\Core\Application\Bus\Query;
use App\Core\Application\Bus\QueryBus;
use App\Core\Application\Bus\QueryHandler;
use PHPUnit\Framework\TestCase;

final class BusContractTest extends TestCase
{
    public function test_GivenMarkerInterfaces_WhenImplemented_ThenInstanceofPasses(): void
    {
        $cmd = new class implements Command {};
        $qry = new class implements Query {};
        $cmdH = new class implements CommandHandler {};
        $qryH = new class implements QueryHandler {};

        self::assertInstanceOf(Command::class, $cmd);
        self::assertInstanceOf(Query::class, $qry);
        self::assertInstanceOf(CommandHandler::class, $cmdH);
        self::assertInstanceOf(QueryHandler::class, $qryH);
    }

    public function test_GivenBusInterfaces_WhenInspected_ThenExpectedMethodsExist(): void
    {
        self::assertTrue(method_exists(CommandBus::class, 'handle'));
        self::assertTrue(method_exists(QueryBus::class, 'ask'));
    }
}
```

- [ ] **Step 3: Run tests → PASS**

```bash
make tests-unit
```

- [ ] **Step 4: Commit**

```bash
git add src/Core/Application/Bus tests/Unit/Core/Application/Bus
git commit -m "feat(core): add CQRS bus marker interfaces"
```

---

### Task 18: Configurar Symfony Messenger con 3 buses

**Files:** Modify `config/packages/messenger.yaml`, `config/services.yaml`

- [ ] **Step 1: Sobrescribir `config/packages/messenger.yaml`**

```yaml
framework:
    messenger:
        default_bus: command.bus
        buses:
            command.bus:
                middleware:
                    - validation
                    - doctrine_transaction
            query.bus:
                default_middleware:
                    allow_no_handlers: false
                    allow_no_senders: false
            event.bus:
                default_middleware:
                    allow_no_handlers: true
                    allow_no_senders: true
```

- [ ] **Step 2: Añadir `_instanceof` a `config/services.yaml`**

Bajo la clave `services:` añadir (manteniendo lo existente):

```yaml
services:
    _defaults:
        autowire: true
        autoconfigure: true

    _instanceof:
        App\Core\Application\Bus\CommandHandler:
            tags:
                - { name: messenger.message_handler, bus: command.bus }
        App\Core\Application\Bus\QueryHandler:
            tags:
                - { name: messenger.message_handler, bus: query.bus }

    App\:
        resource: '../src/'
        exclude:
            - '../src/DependencyInjection/'
            - '../src/Entity/'
            - '../src/Kernel.php'

    # Bind nuestras interfaces de bus a la implementación de Messenger
    App\Core\Application\Bus\CommandBus:
        class: App\Shared\Infrastructure\Bus\MessengerCommandBus
        arguments: ['@messenger.bus.command.bus']

    App\Core\Application\Bus\QueryBus:
        class: App\Shared\Infrastructure\Bus\MessengerQueryBus
        arguments: ['@messenger.bus.query.bus']
```

- [ ] **Step 3: Crear adaptadores Messenger**

`src/Shared/Infrastructure/Bus/MessengerCommandBus.php`:

```php
<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Bus;

use App\Core\Application\Bus\Command;
use App\Core\Application\Bus\CommandBus;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\HandleTrait;

final class MessengerCommandBus implements CommandBus
{
    use HandleTrait;

    public function __construct(MessageBusInterface $commandBus)
    {
        $this->messageBus = $commandBus;
    }

    public function handle(Command $command): void
    {
        $this->handle($command);
    }
}
```

Hmm — colisión de nombres con `HandleTrait::handle`. Renombrar el método de interfaz:

Cambiar en `src/Core/Application/Bus/CommandBus.php`:

```php
interface CommandBus
{
    public function dispatch(Command $command): void;
}
```

Y en `MessengerCommandBus`:

```php
public function dispatch(Command $command): void
{
    $this->handle($command);
}
```

`src/Shared/Infrastructure/Bus/MessengerQueryBus.php`:

```php
<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Bus;

use App\Core\Application\Bus\Query;
use App\Core\Application\Bus\QueryBus;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\HandleTrait;

final class MessengerQueryBus implements QueryBus
{
    use HandleTrait;

    public function __construct(MessageBusInterface $queryBus)
    {
        $this->messageBus = $queryBus;
    }

    public function ask(Query $query): mixed
    {
        return $this->handle($query);
    }
}
```

- [ ] **Step 4: Actualizar BusContractTest**

Cambiar `method_exists(CommandBus::class, 'handle')` a `'dispatch'`.

- [ ] **Step 5: Run tests + cache clear**

```bash
make bin-console ARGS="cache:clear"
make tests-unit
```

Expected: PASS.

- [ ] **Step 6: Commit**

```bash
git add config src/Shared/Infrastructure/Bus src/Core/Application/Bus tests
git commit -m "feat(messenger): wire 3 buses with autotagging instanceof"
```

---

### Task 19: `SecurityToken` DTO + interfaces de autorización

**Files:** Create `src/Core/Application/DTO/Security/SecurityToken.php`, `src/Core/Application/Command/Common/Security/{SecurableHandler.php, SecurityAwareTrait.php}`, `src/Core/Domain/Service/Security/{SecurityChecker.php, PermissiveSecurityChecker.php}`, `src/Core/Domain/Exception/Security/ForbiddenException.php`

- [ ] **Step 1: Crear `src/Core/Application/DTO/Security/SecurityToken.php`**

```php
<?php

declare(strict_types=1);

namespace App\Core\Application\DTO\Security;

use App\Shared\Domain\Enum\SystemRole;

final class SecurityToken
{
    public function __construct(
        public readonly string $authUserId,
        public readonly SystemRole $role = SystemRole::EMPLOYEE,
    ) {
    }
}
```

- [ ] **Step 2: Crear `src/Core/Domain/Exception/Security/ForbiddenException.php`**

```php
<?php

declare(strict_types=1);

namespace App\Core\Domain\Exception\Security;

use App\Shared\Domain\Model\CustomException;
use App\Shared\Domain\Model\ErrorCode;
use Throwable;

final class ForbiddenException extends CustomException
{
    public function __construct(string $actionType = '', ?Throwable $previous = null)
    {
        parent::__construct(
            message: 'TR_FORBIDDEN_ACCESS',
            errorCode: ErrorCode::FORBIDDEN,
            messageParams: $actionType !== '' ? ['%actionType%' => $actionType] : [],
            previous: $previous,
        );
    }
}
```

- [ ] **Step 3: Crear `src/Core/Domain/Service/Security/SecurityChecker.php`** (interfaz)

```php
<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\Security;

use App\Core\Application\DTO\Security\SecurityToken;
use App\Core\Domain\Exception\Security\ForbiddenException;

interface SecurityChecker
{
    /** @throws ForbiddenException */
    public function grants(SecurityToken $securityToken, object $subject): void;
}
```

- [ ] **Step 4: Crear `src/Core/Domain/Service/Security/PermissiveSecurityChecker.php`**

```php
<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\Security;

use App\Core\Application\DTO\Security\SecurityToken;

final class PermissiveSecurityChecker implements SecurityChecker
{
    public function grants(SecurityToken $securityToken, object $subject): void
    {
        // Provisional: permite todo. Se sustituye en Plan 6 (Fase 3).
    }
}
```

- [ ] **Step 5: Crear `src/Core/Application/Command/Common/Security/SecurableHandler.php`**

```php
<?php

declare(strict_types=1);

namespace App\Core\Application\Command\Common\Security;

use App\Core\Application\DTO\Security\SecurityToken;
use App\Core\Domain\Exception\Security\ForbiddenException;
use App\Core\Domain\Service\Security\SecurityChecker;

interface SecurableHandler
{
    public function securityChecker(): SecurityChecker;

    /** @throws ForbiddenException */
    public function checkSecurity(SecurityToken $securityToken, object $subject): void;
}
```

- [ ] **Step 6: Crear `src/Core/Application/Command/Common/Security/SecurityAwareTrait.php`**

```php
<?php

declare(strict_types=1);

namespace App\Core\Application\Command\Common\Security;

use App\Core\Application\DTO\Security\SecurityToken;
use App\Shared\Domain\Enum\SystemRole;

trait SecurityAwareTrait
{
    public function checkSecurity(SecurityToken $securityToken, object $subject): void
    {
        if ($securityToken->role === SystemRole::ADMIN) {
            return;
        }

        $this->securityChecker()->grants($securityToken, $subject);
    }
}
```

- [ ] **Step 7: Test rápido del trait**

`tests/Unit/Core/Application/Command/Common/Security/SecurityAwareTraitTest.php`:

```php
<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Application\Command\Common\Security;

use App\Core\Application\Command\Common\Security\SecurableHandler;
use App\Core\Application\Command\Common\Security\SecurityAwareTrait;
use App\Core\Application\DTO\Security\SecurityToken;
use App\Core\Domain\Service\Security\SecurityChecker;
use App\Shared\Domain\Enum\SystemRole;
use PHPUnit\Framework\TestCase;
use stdClass;

final class SecurityAwareTraitTest extends TestCase
{
    public function test_GivenAdminToken_WhenCheckSecurity_ThenCheckerIsBypassed(): void
    {
        $checker = $this->createMock(SecurityChecker::class);
        $checker->expects(self::never())->method('grants');

        $handler = $this->makeHandler($checker);
        $token = new SecurityToken('id', SystemRole::ADMIN);

        $handler->checkSecurity($token, new stdClass());
    }

    public function test_GivenEmployeeToken_WhenCheckSecurity_ThenCheckerIsCalled(): void
    {
        $checker = $this->createMock(SecurityChecker::class);
        $checker->expects(self::once())->method('grants');

        $handler = $this->makeHandler($checker);
        $token = new SecurityToken('id', SystemRole::EMPLOYEE);

        $handler->checkSecurity($token, new stdClass());
    }

    private function makeHandler(SecurityChecker $checker): SecurableHandler
    {
        return new class($checker) implements SecurableHandler {
            use SecurityAwareTrait;
            public function __construct(private readonly SecurityChecker $checker) {}
            public function securityChecker(): SecurityChecker { return $this->checker; }
        };
    }
}
```

- [ ] **Step 8: Run tests → PASS**

- [ ] **Step 9: Commit**

```bash
git add src/Core/Application/Command tests/Unit/Core/Application/Command
git add src/Core/Application/DTO src/Core/Domain/Service/Security src/Core/Domain/Exception/Security
git commit -m "feat(core): add SecurityToken, SecurableHandler, SecurityChecker (permissive)"
```

---

### Task 20: SecurityTokenExtractor (JWT)

**Files:** Create `src/App/Auth/Domain/Service/SecurityTokenExtractorInterface.php`, `src/App/Auth/Infrastructure/JwtSecurityTokenExtractor.php`

- [ ] **Step 1: Crear interfaz**

```php
<?php

declare(strict_types=1);

namespace App\App\Auth\Domain\Service;

use App\Core\Application\DTO\Security\SecurityToken;

interface SecurityTokenExtractorInterface
{
    public function __invoke(): SecurityToken;
}
```

- [ ] **Step 2: Crear implementación**

```php
<?php

declare(strict_types=1);

namespace App\App\Auth\Infrastructure;

use App\App\Auth\Domain\Service\SecurityTokenExtractorInterface;
use App\Core\Application\DTO\Security\SecurityToken;
use App\Shared\Domain\Enum\SystemRole;
use App\UserManagement\Domain\AppUser;
use Symfony\Bundle\SecurityBundle\Security;

final class JwtSecurityTokenExtractor implements SecurityTokenExtractorInterface
{
    public function __construct(private readonly Security $security) {}

    public function __invoke(): SecurityToken
    {
        $user = $this->security->getUser();

        if (!$user instanceof AppUser) {
            return new SecurityToken('anonymous', SystemRole::EMPLOYEE);
        }

        return new SecurityToken(
            authUserId: (string) $user->getId(),
            role: $user->getRole(),
        );
    }
}
```

> **Nota legacy**: importa la entidad vieja `App\UserManagement\Domain\AppUser`. En Plan 2 (Lote A · slice User) este import se cambia a `App\Core\Domain\Model\Aggregate\User`.

- [ ] **Step 3: Test funcional con JWT real**

`tests/Functional/App/Auth/Infrastructure/JwtSecurityTokenExtractorTest.php`:

```php
<?php

declare(strict_types=1);

namespace App\Tests\Functional\App\Auth\Infrastructure;

use App\App\Auth\Domain\Service\SecurityTokenExtractorInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class JwtSecurityTokenExtractorTest extends KernelTestCase
{
    public function test_GivenNoAuthenticatedUser_WhenInvoke_ThenReturnsAnonymousToken(): void
    {
        self::bootKernel();
        $extractor = self::getContainer()->get(SecurityTokenExtractorInterface::class);

        $token = $extractor();

        self::assertSame('anonymous', $token->authUserId);
    }
}
```

- [ ] **Step 4: Run tests-functional → PASS**

```bash
make tests-functional
```

- [ ] **Step 5: Commit**

```bash
git add src/App/Auth tests/Functional/App
git commit -m "feat(auth): add JWT security token extractor"
```

---

### Task 21: ExceptionListener (NO suscrito todavía)

**Files:** Create `src/App/UI/API/Response/Model/JsonContentErrorResponse.php`, `src/App/UI/API/Response/Service/{ExceptionListener.php, MapperExceptionToJsonErrorResponse.php, GetCurrentEnvironment.php}`

- [ ] **Step 1: Crear `JsonContentErrorResponse`**

```php
<?php

declare(strict_types=1);

namespace App\App\UI\API\Response\Model;

final readonly class JsonContentErrorResponse
{
    public function __construct(
        public string $code,
        public string $message = '',
    ) {}
}
```

- [ ] **Step 2: Crear `GetCurrentEnvironment`**

```php
<?php

declare(strict_types=1);

namespace App\App\UI\API\Response\Service;

final readonly class GetCurrentEnvironment
{
    public function __construct(private string $environment) {}

    public function __invoke(): string
    {
        return $this->environment;
    }
}
```

Wire en `services.yaml`:

```yaml
    App\App\UI\API\Response\Service\GetCurrentEnvironment:
        arguments: ['%kernel.environment%']
```

- [ ] **Step 3: Crear `MapperExceptionToJsonErrorResponse`** (con mapeos vacíos por ahora — se rellena en Plan 6)

```php
<?php

declare(strict_types=1);

namespace App\App\UI\API\Response\Service;

use App\App\UI\API\Response\Model\JsonContentErrorResponse;
use App\Core\Domain\Exception\Security\ForbiddenException;
use App\Shared\Domain\Model\CustomException;
use App\Shared\Domain\Model\ErrorCode;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final readonly class MapperExceptionToJsonErrorResponse
{
    public function __invoke(Throwable $exception, bool $returnGenericUnexpectedError = false): ?JsonResponse
    {
        if ($exception instanceof ForbiddenException) {
            return $this->build($exception, Response::HTTP_FORBIDDEN);
        }

        if ($exception instanceof CustomException) {
            // Plan 6 (Fase 3) expande este switch con todas las excepciones de dominio
            return $this->build($exception, Response::HTTP_BAD_REQUEST);
        }

        if ($returnGenericUnexpectedError) {
            return new JsonResponse(
                new JsonContentErrorResponse(ErrorCode::UNEXPECTED_ERROR->value, ''),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return null;
    }

    private function build(CustomException $e, int $status): JsonResponse
    {
        return new JsonResponse(
            new JsonContentErrorResponse($e->errorCode->value, $e->getMessage()),
            $status
        );
    }
}
```

- [ ] **Step 4: Crear `ExceptionListener`** (sin suscribir aún)

```php
<?php

declare(strict_types=1);

namespace App\App\UI\API\Response\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

final readonly class ExceptionListener
{
    public function __construct(
        private GetCurrentEnvironment $getCurrentEnvironment,
        private MapperExceptionToJsonErrorResponse $mapper,
        private LoggerInterface $logger,
    ) {}

    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $this->logger->error('Unhandled exception', [
            'message' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);

        $env = ($this->getCurrentEnvironment)();
        if ($env === 'dev') {
            return;
        }

        $response = ($this->mapper)($exception, returnGenericUnexpectedError: $env === 'prod');
        if ($response !== null) {
            $event->setResponse($response);
        }
    }
}
```

> **Importante**: aún NO añadimos el tag `kernel.event_listener` ni el `kernel.event_subscriber`. Plan 6 lo suscribe.

- [ ] **Step 5: Test smoke del listener desuscrito**

`tests/Unit/App/UI/API/Response/Service/ExceptionListenerSmokeTest.php`:

```php
<?php

declare(strict_types=1);

namespace App\Tests\Unit\App\UI\API\Response\Service;

use App\App\UI\API\Response\Service\ExceptionListener;
use App\App\UI\API\Response\Service\GetCurrentEnvironment;
use App\App\UI\API\Response\Service\MapperExceptionToJsonErrorResponse;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

final class ExceptionListenerSmokeTest extends TestCase
{
    public function test_GivenDependencies_WhenConstructed_ThenInstanceIsCreated(): void
    {
        $listener = new ExceptionListener(
            new GetCurrentEnvironment('dev'),
            new MapperExceptionToJsonErrorResponse(),
            new NullLogger(),
        );
        self::assertInstanceOf(ExceptionListener::class, $listener);
    }
}
```

- [ ] **Step 6: Run tests → PASS**

- [ ] **Step 7: Commit**

```bash
git add src/App/UI tests/Unit/App/UI config/services.yaml
git commit -m "feat(app): add inactive ExceptionListener + mapper scaffolding"
```

---

### Task 22: Configurar Doctrine para XML mapping en `App\Core`

**Files:** Modify `config/packages/doctrine.yaml`

- [ ] **Step 1: Editar `config/packages/doctrine.yaml`**

Añadir bajo `orm.mappings`:

```yaml
doctrine:
    orm:
        # ... mapeo existente para App\UserManagement, etc. SE MANTIENE durante Plan 1
        mappings:
            Core:
                is_bundle: false
                type: xml
                dir: '%kernel.project_dir%/src/Core/Infrastructure/Persistence/Doctrine/ORM/Mapping/XML'
                prefix: 'App\Core\Domain\Model\Aggregate'
                alias: Core
```

> Durante Plan 1 el directorio XML existe vacío. Los mappings reales se añaden en los Plans 2-5.

- [ ] **Step 2: Crear directorio vacío**

```bash
mkdir -p src/Core/Infrastructure/Persistence/Doctrine/ORM/Mapping/XML
touch src/Core/Infrastructure/Persistence/Doctrine/ORM/Mapping/XML/.gitkeep
```

- [ ] **Step 3: Verificar `doctrine:schema:validate` no rompe**

```bash
make bin-console ARGS="doctrine:schema:validate --skip-sync"
```

Expected: el mapping de Core aparece como "OK" o "no entities found" (válido); el mapping viejo sigue funcionando.

- [ ] **Step 4: Commit**

```bash
git add config/packages/doctrine.yaml src/Core/Infrastructure
git commit -m "chore(doctrine): register XML mapping autodiscovery for App\\Core"
```

---

## GATE A — Verificación de fin de plan

### Task 23: Verificación final

- [ ] **Step 1: phpstan limpio**

```bash
make composer ARGS="phpstan"
```

Expected: 0 errors.

- [ ] **Step 2: deptrac limpio (con baseline si aplica)**

```bash
make composer ARGS="deptrac"
```

Expected: 0 violations (las del código legacy quedan absorbidas por la baseline).

- [ ] **Step 3: tests-unit verdes**

```bash
make tests-unit
```

Expected: todos los tests añadidos en Plan 1 pasan.

- [ ] **Step 4: tests-functional verdes**

```bash
make tests-functional
```

Expected: PASS.

- [ ] **Step 5: App vieja sigue respondiendo**

```bash
curl -X POST http://localhost/480project/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password1234"}'
```

Expected: 200 con `token`.

- [ ] **Step 6: CI GitHub Actions verde**

```bash
git push origin feature/ddd-refactor
```

Comprobar en https://github.com/ionleon/inventario-480project-ionleon-back/actions que el workflow termina verde.

- [ ] **Step 7: Tag de fin de Plan 1**

```bash
git tag -a plan-01-complete -m "Plan 1: plataforma + infra transversal completos (Gate A)"
git push origin plan-01-complete
```

---

## Siguiente plan

Plan 2: **Lote A — Slices verticales de los 5 aggregates sin dependencias** (User, Sector, Technology, ProjectRole, RefreshToken). Documento: `2026-05-14-02-lote-a-slices.md`.
