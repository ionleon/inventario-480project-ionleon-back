# Inventario 480 Project — Backend

API REST en **Symfony 7.3 + PHP 8.3** sobre **FrankenPHP**, **PostgreSQL 17** y **JWT (Lexik)**. Arquitectura **DDD por capas + CQRS** (ver `arquitectura-general/`).

## Requisitos

- Docker Desktop + plugin `compose` v2
- `make`
- (Opcional) Cuenta GitHub SSH si vas a clonar/push

## Arranque

```bash
make start
```

Esto:

1. Levanta los 3 contenedores (`inventario480-api` con FrankenPHP, `inventario480-db` Postgres 17, `inventario480-db-test` Postgres 17 en puerto 5433 para tests E2E).
2. Instala dependencias Composer.
3. Genera el par de claves JWT (`config/jwt/{private,public}.pem`).
4. Crea la BD y ejecuta migraciones.
5. Carga fixtures (admin + employee + datos de ejemplo).

App disponible en `http://localhost` (con redirect automático a HTTPS por Caddy).

### Credenciales seed

- `admin@example.com` / `password1234` (rol `ROLE_ADMIN`)
- `dev@example.com` / `password1234` (rol `ROLE_EMPLOYEE`)
- `inactive@example.com` / `password1234` (inactivo)

## Estructura del código

```
src/
├── App/                                # UI / boundary externo
│   ├── UI/API/Controller/<Aggregate>/  # Controllers invocables por endpoint
│   ├── UI/API/Response/                # ExceptionListener + DTOs error
│   ├── Auth/                           # JWT extractor, listeners de seguridad
│   └── Security/                       # Token blacklist
├── Core/                               # Bounded context principal
│   ├── Domain/
│   │   ├── AggregateRoot.php           # Base con recordEvent/pullEvents
│   │   ├── Model/Aggregate/            # 11 aggregates: User, Sector, Technology,
│   │   │                               #   ProjectRole, RefreshToken, Client, Contact,
│   │   │                               #   Project, Link, ProjectUser, TimeEntry
│   │   ├── Model/Event/<Aggregate>/    # Eventos de dominio (XxxWasCreated, …)
│   │   ├── Model/VO/<Aggregate>/       # Value Objects (XxxId, XxxName, …)
│   │   ├── Model/VO/Common/            # Email, Phone, Url, Password
│   │   ├── Model/Repository/           # Interfaces de repositorio
│   │   ├── Service/<Aggregate>/        # Servicios de dominio
│   │   ├── Service/Security/           # SecurityChecker
│   │   ├── Exception/                  # Excepciones de dominio
│   │   └── DTO/Security/SecurityToken  # DTO de autenticación
│   ├── Application/
│   │   ├── Bus/                        # CommandBus, QueryBus, marker interfaces
│   │   ├── Command/<Aggregate>/        # Commands + Handlers
│   │   ├── Query/<Aggregate>/          # Queries + Handlers
│   │   └── EventSubscriber/            # Subscribers cross-aggregate
│   └── Infrastructure/
│       └── Persistence/Doctrine/
│           ├── DBAL/Types/             # 25+ tipos custom (XxxIdType, …)
│           ├── ORM/Mapping/XML/        # Mapeos XML por aggregate
│           ├── ORM/Orm<Aggregate>Repository.php
│           └── EventDispatcher.php     # postFlush → event.bus
└── Shared/
    └── Domain/Model/                   # CustomException, ErrorCode enum
```

## Comandos habituales

```bash
make bash                                  # shell en el contenedor api
make bin-console ARGS="cache:clear"        # cualquier comando Symfony
make migrations-migrate                    # aplica migraciones pendientes
make migrations-diff                       # genera migration desde diff de mapping
make code-quality                          # phpcs + phpstan + deptrac
make tests-unit                            # PHPUnit suite Unit (324 tests)
make tests-api                             # Codeception suite Api E2E (23 tests)
make tests-all                             # todo
```

Lista completa: `make help`.

## Tests

| Suite | Comando | Cantidad | Cobertura |
|---|---|---|---|
| **Unit** | `make tests-unit` | 331 tests | VOs, Aggregates (factory + transitions), Domain Services, Handlers (incluido ownership EMPLOYEE en TimeEntry), Security trait, AggregateId immutability |
| **API E2E** | `make tests-api` | 23 tests | smoke 1-2 por aggregate (happy + error) + cascade `UserWasDeactivated → ProjectUser` |

Las suites E2E corren contra una BD aislada (`inventario480-db-test` en puerto 5433) con fixtures recargadas antes de cada ejecución (extension `DbMigrationExtension`).

## Calidad

```bash
make code-quality
```

- **phpcs** — PSR-12 (`phpcs.xml.dist`), 0 errores
- **phpstan** nivel 6 — 0 errores, sin baseline; los Testers de Codeception se analizan de verdad (typos en `$I->...` rompen el build)
- **deptrac** — 0 violaciones (con 51 skipped intencionales para el patrón Query handler→App response)

## Arquitectura

DDD por capas + CQRS. Para añadir una feature:

1. Lee `arquitectura-general/04-anadir-una-feature.md`.
2. Crea VOs en `src/Core/Domain/Model/VO/<Aggregate>/`.
3. Si añades una acción nueva: Domain Service en `src/Core/Domain/Service/<Aggregate>/<Action>/`.
4. Command/Query + Handler en `src/Core/Application/{Command,Query}/<Aggregate>/<Action>/`.
5. Controller invocable + Request/Response en `src/App/UI/API/Controller/<Aggregate>/<Action>/`.
6. Tests:
   - Unitarios en `tests/Unit/Core/...` (VO, Service, Handler)
   - Cest E2E en `tests/Api/<Aggregate>/<Action>Cest.php`

Lee también:

- `arquitectura-general/05-autorizacion-y-errores.md` — `SecurableHandler`, `SecurityChecker`, excepciones de dominio
- `arquitectura-general/09-testing.md` — Mothers + Cest patterns
- `arquitectura-general/10-errores-comunes.md` — antipatrones a evitar
- `docs/superpowers/specs/2026-05-14-arquitectura-ddd-cqrs-design.md` — spec del refactor
- `docs/superpowers/specs/2026-05-16-refactor-deviations.md` — desviaciones documentadas

## Endpoints

Prefijo global: `/480project`.

| Aggregate | Endpoints |
|---|---|
| Auth | `POST /login`, `POST /token/refresh`, `POST /logout` |
| User | `GET/POST /users`, `GET/PUT/DELETE/PATCH /users/{id}`, `PUT /users/{id}/password-change`, `PUT /users/{id}/admin-password`, `POST /users/{id}/force-logout` |
| Sector | `GET/POST /sectors`, `GET/PATCH/DELETE /sectors/{id}` |
| Client | `GET/POST /clients`, `GET/PUT/DELETE/PATCH /clients/{id}` (PATCH = toggle activación) |
| Contact | `GET/POST /clients/{id}/contacts`, `PATCH/DELETE /clients/{id}/contacts/{cid}`, `PATCH /clients/{id}/contacts/{cid}/main` |
| Technology | `GET/POST /technologies`, `DELETE /technologies/{id}` |
| Project | `GET/POST /projects`, `GET/PUT/DELETE/PATCH /projects/{id}` (PATCH = toggle), `PATCH /projects/{id}/development` |
| Link | `GET/POST /links`, `GET/PUT/DELETE /links/{id}`, `GET /projects/{projectId}/links` |
| ProjectRole | `GET/POST /project-roles`, `DELETE /project-roles/{id}` |
| ProjectUser | `GET/POST/PUT /projects/{id}/users`, `PUT/PATCH/DELETE /projects/{id}/users/{userId}` |
| TimeEntry | `POST /projects/{id}/time-entries`, `POST /users/{id}/time-entries`, `GET/PUT/DELETE /time-entries/{id}`, `GET /projects/{id}/time-entries`, `GET /users/{userId}/time-entries` |

Toda escritura requiere JWT en `Authorization: Bearer <token>`. Las acciones `POST/PUT/DELETE/PATCH` están gateadas por `SecurityChecker`:

- `ROLE_ADMIN`: bypass total.
- `ROLE_EMPLOYEE`: solo puede actuar sobre recursos cuyo `UserId` dueño coincide con el suyo. Para `TimeEntry`, el handler resuelve el dueño vía `TimeEntryRepository::findOwnerUserId` y se lo pasa al checker — un EMPLOYEE no puede tocar las time entries de otro empleado.

## Autenticación de prueba

```bash
curl -k -X POST https://localhost/480project/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password1234"}'
```

Devuelve `{ "token": "...", "refresh_token": "..." }`. Usa el `token` como `Bearer` para las llamadas autenticadas; el `refresh_token` para renovar via `POST /token/refresh`.

## Eventos de dominio

Los aggregates registran eventos en sus mutadores (`$this->recordEvent(...)`). Tras `EntityManager::flush()`, el `App\Core\Infrastructure\Persistence\Doctrine\EventDispatcher` postFlush listener los recolecta y despacha al `event.bus` de Symfony Messenger.

Subscribers actuales:

- `UserWasDeactivatedSubscriber` (`src/Core/Application/EventSubscriber/ProjectUser/`) — cuando un User se desactiva, todos sus `ProjectUser` activos pasan a inactivos. Cubierto por `tests/Api/User/UserDeactivationCascadeCest.php`.

## Migraciones

Todas las migraciones están en `migrations/`. Las del refactor DDD (mayo 2026) son:

- `Version20260430141542` — schema base legacy (preserva tablas existentes)
- `Version20260505094723`, `Version20260506113020` — fixes legacy
- `Version20260515000001` — añade columnas `project.{manager_id, end_date, development_*}` + tabla pivote `project_technology`
- `Version20260515000002` — `link.{project_id, label, created_at}`
- `Version20260515000003` — `project_user.allocation`
- `Version20260516000001` — relaja NOT NULL en `link.{enviroment, development_id}` legacy
- `Version20260517000001/2` — alineación final: drop FKs legacy, drop tabla `development` huérfana, ajustes de tipos VARCHAR

Si vas a aplicar a una BD que NO sea limpia, revisa primero la última migración — borra constraints y la tabla `development`.

## Problemas comunes

- **`make start` se cuelga en `composer install`**: el primer arranque baja muchas dependencias. Si tarda más de 5 min, revisa logs con `docker compose logs -f`.
- **Login 401 sin JWT**: las claves JWT no se generaron. `make bin-console ARGS="lexik:jwt:generate-keypair --skip-if-exists"`.
- **`doctrine:schema:validate` falla**: revisa migraciones pendientes con `make migrations-status` y aplica con `make migrations-migrate`.
- **CORS**: la app permite por defecto `localhost`/`127.0.0.1` en cualquier puerto. Si el frontend va en otro origen, ajusta `CORS_ALLOW_ORIGIN` en `.env.local`.

## Trabajos pendientes / mejoras conocidas

Ver `docs/superpowers/specs/2026-05-16-refactor-deviations.md`. Los más relevantes:

- `RoleBasedSecurityChecker` solo permite a EMPLOYEE actuar sobre su propio `UserId` y sobre `TimeEntryId`. Si el frontend espera otras acciones de empleado (ej. registrar Contact en cliente de su sector), hay que extender las reglas.
- Namespace `App\App\Auth\...` es estéticamente redundante (consecuencia de tener PSR-4 `App\` mapeado a `src/` y carpeta `src/App/`). No afecta funcionalmente.
