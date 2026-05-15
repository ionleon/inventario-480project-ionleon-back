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
