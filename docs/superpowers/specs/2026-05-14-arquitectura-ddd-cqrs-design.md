# Plan de cumplimiento de la arquitectura DDD/CQRS documentada

**Fecha**: 2026-05-14
**Branch destino**: `feature/ddd-refactor`
**Tipo de cambio**: refactor estructural mayor + adopción de runtime + CI nueva
**Documentación de referencia**: `arquitectura-general/01..10-*.md` y `arquitectura-general/docker/`

---

## 1. Resumen ejecutivo

El proyecto debe pasar de su organización actual por **6 bounded contexts paralelos** (`Auth/`, `UserManagement/`, `ClientManagement/`, `ProjectManagement/`, `TimeManagement/`, `Shared/`) con entidades anémicas, handlers sin bus, autorización por `IsGranted` en controllers y runtime PHP-FPM+Nginx, a la arquitectura documentada en `arquitectura-general/`:

- **Un único `Core/` consolidado** con todos los aggregates como hermanos
- **DDD por capas** estricto (Domain · Application · Infrastructure · App) con dependencias hacia dentro
- **CQRS** con `CommandBus`/`QueryBus` (Symfony Messenger) y autowiring por marker interfaces
- **Value Objects** en toda la firma de dominio, primitivos solo en Commands
- **DBAL custom types** por cada VO no nativo
- **Aggregates ricos** (11 en total: `User`, `Sector`, `Technology`, `ProjectRole`, `RefreshToken`, `Client`, `Contact`, `Link`, `Project`, `ProjectUser`, `TimeEntry`) con factory `create()`, mutadores con nombre de negocio y eventos `Was*`
- **Domain Services** entre Handler y Aggregate
- **Mapping XML** en `Core/Infrastructure/Persistence/Doctrine/ORM/Mapping/XML/`
- **Autorización por handler** con `SecurableHandler` + `SecurityChecker`
- **ExceptionListener** que traduce excepciones de dominio a JSON `{code,message}` con HTTP correcto
- **Runtime FrankenPHP** sustituyendo PHP-FPM+Nginx
- **CI en GitHub Actions** con lint + phpstan + tests unitarios + Codeception API
- **Codeception** sustituyendo (o complementando) PHPUnit para tests E2E

El plan se ejecuta como **slices verticales por aggregate en lotes paralelos**, maximizando concurrencia respetando las dependencias entre IDs de aggregates.

---

## 2. Decisiones fundacionales

| # | Decisión | Justificación |
|---|---|---|
| 1 | Consolidar todo en `src/Core/` (no multi-contexto) | El usuario eligió la lectura literal de la doc (capítulo 01: "Core/ — bounded context principal") |
| 2 | Namespace raíz `App\` se mantiene; cambian las sub-rutas | PSR-4 ya correcto; evita tocar `composer.json` |
| 3 | Bus = Symfony Messenger con `command.bus`, `query.bus` y `event.bus`, todos síncronos | Estándar Symfony; los dos primeros para CQRS, el tercero para despachar eventos de dominio tras `postFlush` (capítulo 06); permite extender a async sin reescribir |
| 4 | Marker interfaces propias `App\Core\Application\Bus\{Command,Query,*Handler,*Bus}` | La doc usa marker interfaces explícitas (capítulo 01) |
| 5 | Autowiring por `_instanceof` en `services.yaml` con tag `messenger.message_handler` | La doc dice "el autowiring es automático" |
| 6 | Mapping ORM = XML (no atributos) | La doc lo exige en capítulo 07 |
| 7 | Las tablas y columnas de BD NO se renombran | Cero migraciones SQL; frontend no se rompe |
| 8 | Auth NO entra en `Core/`: el extractor JWT vive en `App/Auth/`, los servicios JWT en `App/Security/` | La doc ubica `SecurityTokenExtractor` en `App/Auth/Domain/Service/` |
| 9 | `RefreshToken` sí es aggregate y vive en `Core/Domain/Model/Aggregate/` | Es un agregado persistido con ciclo de vida propio |
| 10 | `SecurityChecker` arranca como `PermissiveSecurityChecker` (permite todo); se sustituye en Fase 3 | Desbloquea el refactor sin negociar políticas de permisos |
| 11 | `ErrorCode` enum pre-rellenado en Fase 1 con todos los cases previsibles para los 11 aggregates | Evita conflictos de merge entre agentes paralelos |
| 12 | Boundary enforcement con `qossmic/deptrac-shim` en CI | La doc obliga a que Domain no importe Doctrine/HTTP |
| 13 | Estrategia de migración = vertical slices por aggregate en lotes paralelos | Decisión del usuario; sustituye conscientemente a "capa por capa" para maximizar paralelismo |
| 14 | Runtime = FrankenPHP 1.5 / PHP 8.3 (classic en dev, worker en CI/E2E) | El ejemplo `arquitectura-general/docker/` lo asume; el `bitbucket-pipelines.yml` de referencia usa `worker.caddyfile` |
| 15 | Codeception es el framework de tests para Unit + Functional + Api; PHPUnit deja de invocarse directamente | El capítulo 09 usa Codeception (`Cest`, `ApiTester`, `Codeception\Test\Unit`); mantener dos frameworks duplicaría infraestructura. PHPUnit queda como dependencia transitiva mientras los tests existentes (1 sólo: `CreateUserHandlerTest.php`) se migran a Codeception Unit en Fase 0. |
| 16 | `Development` NO es aggregate propio: es una entidad-valor dentro del aggregate `Project` | Solo tiene una acción de escritura (`UpdateDevelopment`) y no tiene ciclo de vida independiente. La acción se modela como mutador del aggregate Project (`Project::updateDevelopment(...)`). Reduce Fase 2 a 11 aggregates. |
| 17 | `Link` es aggregate propio con referencia por VO `ProjectId` | Tiene controllers propios (`CreateLink`, `DeleteLink`) y se gestiona independiente del Project. Si el proyecto se borra, los Links asociados se borran por subscriber de `ProjectWasDeleted`. |

---

## 3. Estado actual vs arquitectura objetivo

| Aspecto | Estado actual | Target documentado |
|---|---|---|
| Estructura raíz | 6 contextos paralelos | Un solo `Core/` + `App/` + `Shared/` |
| Controllers | `<Context>/Infrastructure/Http/` con `fromRequest()` manual + try/catch + `IsGranted` | `App/UI/API/Controller/<Aggregate>/<Action>/` invocable con `#[MapRequestPayload]`, sin try/catch |
| Buses | Sin bus — `$handler->handle()` directo | `CommandBus`/`QueryBus` con autowiring por marker interface |
| Aggregates | Entidades anémicas con setters, anotaciones Doctrine y `Serializer Groups` mezclados | Aggregates ricos con factory `create()`, eventos `Was*`, sin anotaciones de UI/persistencia |
| Value Objects | Casi inexistentes (solo `Password` en UserManagement) | VOs en toda firma de dominio; primitivos solo en Commands |
| DBAL custom types | No hay | Uno por VO no nativo |
| Domain Services | No existen — los Handlers hacen todo | Capa entre Handler y Aggregate, con interfaz |
| Mapping ORM | Atributos PHP sobre la entidad | XML bajo `Infrastructure/Persistence/Doctrine/ORM/Mapping/XML/` |
| Eventos de dominio | Solo `UserActivationChanged`, no estándar | `recordEvent()` desde el aggregate; naming `<Aggregate>Was*`; despacho post-flush |
| Autorización | `#[IsGranted('ROLE_ADMIN')]` en controllers | `SecurableHandler` + `SecurityChecker::grants()` en handler |
| Excepciones | `try/catch` en controllers, `\DomainException` genérica | `CustomException` con `ErrorCode` + `messageParams`; traducción a HTTP por listener |
| Tests | PHPUnit con muy poca cobertura | Codeception (Unit/Functional/Api) con Mothers + Cest; cobertura por checklist cap. 09 |
| Runtime | PHP-FPM 8.3 + Nginx | FrankenPHP 1.5 + Caddy (classic/worker) |
| CI | No hay | GitHub Actions con composer install + linting + phpstan + tests por suite |
| Cross-context | `DoctrineUserRepository::updateUserActivationWithRelation` toca `ProjectUser` directamente | Comunicación cross-aggregate por eventos de dominio |

---

## 4. Stack objetivo

### 4.1 Estructura de directorios destino

```
src/
├── App/
│   ├── UI/
│   │   └── API/
│   │       ├── Controller/<Aggregate>/<Action>/{Action}Controller.php
│   │       │                                   {Action}Request.php
│   │       │                                   {Action}Response.php
│   │       └── Response/
│   │           ├── Model/JsonContentErrorResponse.php
│   │           └── Service/{ExceptionListener,MapperExceptionToJsonErrorResponse,GetCurrentEnvironment}.php
│   ├── Auth/
│   │   ├── Domain/Service/SecurityTokenExtractorInterface.php
│   │   └── Infrastructure/JwtSecurityTokenExtractor.php
│   └── Security/                                       # servicios JWT (lexik wrapper)
│
├── Core/
│   ├── Domain/
│   │   ├── Model/
│   │   │   ├── Aggregate/{User,Client,Sector,Contact,Project,ProjectUser,ProjectRole,Technology,Link,Development,TimeEntry,RefreshToken}.php
│   │   │   ├── AggregateRoot.php
│   │   │   ├── Event/<Aggregate>/{Aggregate}Was{Created,Updated,Deactivated,...}.php
│   │   │   ├── Repository/<Aggregate>Repository.php       # interfaces
│   │   │   └── VO/
│   │   │       ├── <Aggregate>/{<Aggregate>Id, ...}.php
│   │   │       └── Common/{Email, Password, Phone, Url, ...}.php
│   │   ├── Service/
│   │   │   ├── <Aggregate>/<Action>/{Action}Service.php + {Action}ServiceInterface.php
│   │   │   └── Security/SecurityChecker.php
│   │   └── Exception/
│   │       ├── <Aggregate>/{Invalid*,NotFound*,Duplicated*}Exception.php
│   │       └── Security/ForbiddenException.php
│   ├── Application/
│   │   ├── Bus/{Command,Query,CommandHandler,QueryHandler,CommandBus,QueryBus}.php
│   │   ├── Command/
│   │   │   ├── <Aggregate>/<Action>/{Action}{Command,Handler}.php
│   │   │   └── Common/Security/{SecurableHandler.php, SecurityAwareTrait.php}
│   │   ├── Query/<Aggregate>/<Action>/{Action}{Query,Handler}.php
│   │   └── DTO/Security/SecurityToken.php
│   └── Infrastructure/
│       └── Persistence/Doctrine/
│           ├── DBAL/Types/<VO>Type.php
│           └── ORM/
│               ├── Mapping/XML/<Aggregate>.orm.xml
│               └── Orm<Aggregate>Repository.php
│
└── Shared/
    └── Domain/
        └── Model/{CustomException, ErrorCode}.php
```

### 4.2 Stack Docker

```
Dockerfile                                # multi-stage: base → dev → prod (FrankenPHP 1.5 / PHP 8.3 alpine)
compose.yaml                              # dev: api + db + db-test
.dockerignore
.docker/
  caddy/
    classic.caddyfile                     # modo clásico (dev)
    worker.caddyfile                      # modo worker (CI/E2E acelerado)
  php/
    conf.d/zz-php.ini
    conf.d/dev/{zz-php-extend.ini, xdebug.ini}
    conf.d/prod/zz-php-extend.ini
  postgres/
    Dockerfile                            # Postgres 17 alpine (SIN pgvector)
    init/                                 # placeholder
Makefile
.github/
  workflows/
    ci.yml
```

### 4.3 Servicios en `compose.yaml`

- `inventario480-api` (FrankenPHP, build target `dev`, monta `./:/app`, expone 80/443/443udp)
- `inventario480-db` (Postgres 17 alpine, healthcheck, volumen persistente)
- `inventario480-db-test` (Postgres 17 alpine en otro puerto, sin volumen persistente; recreable)

Sin RabbitMQ, sin Supervisor, sin MailDev, sin Zitadel, sin pgvector.

### 4.4 Targets del `Makefile`

```
start                  # up + composer install + dump-env dev + JWT keypair + prepare-dev-db + fixtures
bash                   # shell interactivo en el contenedor api
bin-console ARGS=…     # php bin/console ARGS
prepare-dev-db         # crea DB dev + migrate
prepare-test-db        # crea DB test + migrate
code-quality           # phpcs + phpstan + deptrac
tests-unit             # PHPUnit unitarios + Codeception unit
tests-functional       # Codeception functional
tests-api              # prepare-test-db + Codeception api
tests-all              # los tres
migrations-diff
migrations-migrate
migrations-status
dc-up-d-rebuild
dc-down
```

### 4.5 GitHub Actions workflow (`.github/workflows/ci.yml`)

Triggers: push a cualquier rama + pull_request.

Jobs en este orden (con dependencias):

1. `composer-install` — instala con cache de `~/.composer/cache` y `vendor/`
2. `security-scan` — `symfony local:check:security`
3. `lint` — `phpcs`
4. `phpstan` — `phpstan analyse`
5. `deptrac` — `deptrac analyse`
6. `tests-unit` — sin BD
7. `tests-api` — services: postgres17 alpine como `inventario480-db-test`, arranca FrankenPHP con `worker.caddyfile` en background, corre `codecept run tests/Api`

**Deploy queda explícitamente fuera de alcance.** No se replica Helm/EKS del ejemplo.

---

## 5. Plan de fases (slices verticales con paralelismo)

### Fase 0 — Plataforma (1 agente, **paralelo con Fase 1**)

Entregables:
- `Dockerfile` multi-stage basado en `dunglas/frankenphp:1.5.0-php8.3-alpine` (stages: `base`, `dev` con xdebug, `prod` con opcache prod)
- `compose.yaml` con `inventario480-api`, `inventario480-db`, `inventario480-db-test`
- `.docker/{caddy,php,postgres}/` con configs (sin pgvector, sin supervisor)
- `Makefile` con todos los targets de §4.4
- `composer require --dev codeception/codeception codeception/module-symfony codeception/module-db codeception/module-rest`
- `codeception.yml` + suites `Unit`, `Functional`, `Api`
- `tests/Support/ApiTester.php` esqueleto con stubs para `haveAdminHttpHeaders()`, `with<Aggregate>()`, `seeResponseErrorCodeContent()`, `dontSeeInRepository()`
- `.github/workflows/ci.yml` con los jobs descritos en §4.5
- `.env.test.pipeline` con hosts adaptados al runner
- Adaptar `phpunit.dist.xml`: testsuite `Unit` apuntando a `tests/Unit/`
- Eliminar `Dockerfile` viejo, `docker-compose.yml` viejo, `deploy.sh`
- Reescribir `readme.md` con `make start`

**Criterio de salida**: `make start` arranca la app vieja en FrankenPHP; `make tests-unit` ejecuta (aunque sin tests todavía); workflow de CI verde en una PR de prueba contra una rama desechable.

### Fase 1 — Infraestructura transversal (1 agente, **paralelo con Fase 0**)

Entregables:
- `src/Shared/Domain/Model/CustomException.php`
- `src/Shared/Domain/Model/ErrorCode.php` — **pre-rellenado** con todos los cases por aggregate (Inventario común + cada `INVALID_<X>_ID`, `<X>_NOT_FOUND`, `DUPLICATED_<X>_NAME` para los 11 aggregates)
- `src/Core/Domain/Model/AggregateRoot.php` (campo `pendingEvents`, `recordEvent()`, `pullEvents()`)
- `src/Core/Application/Bus/Command.php`, `Query.php`, `CommandHandler.php`, `QueryHandler.php`, `CommandBus.php`, `QueryBus.php`
- `src/Core/Application/DTO/Security/SecurityToken.php`
- `src/Core/Application/Command/Common/Security/SecurableHandler.php`
- `src/Core/Application/Command/Common/Security/SecurityAwareTrait.php`
- `src/Core/Domain/Service/Security/SecurityChecker.php` (interfaz)
- `src/Core/Domain/Service/Security/PermissiveSecurityChecker.php` (impl. provisional que no lanza nunca)
- `src/Core/Domain/Exception/Security/ForbiddenException.php`
- `src/App/Auth/Domain/Service/SecurityTokenExtractorInterface.php`
- `src/App/Auth/Infrastructure/JwtSecurityTokenExtractor.php` (envuelve el `Symfony\Component\Security\Core\Security` + lexik JWT actual)
- `src/App/UI/API/Response/Model/JsonContentErrorResponse.php`
- `src/App/UI/API/Response/Service/ExceptionListener.php`, `MapperExceptionToJsonErrorResponse.php`, `GetCurrentEnvironment.php` — **declarados pero NO suscritos** a `kernel.exception` aún
- `config/packages/messenger.yaml` con `command.bus` y `query.bus` (síncronos)
- `config/services.yaml` con `_instanceof` que añade tag `messenger.message_handler` con `bus: command.bus` a clases que implementen `CommandHandler`, y análogo para `QueryHandler`
- `config/packages/doctrine.yaml` con autodiscovery XML para namespace `App\Core` apuntando a `%kernel.project_dir%/src/Core/Infrastructure/Persistence/Doctrine/ORM/Mapping/XML`
- `composer require --dev qossmic/deptrac-shim`
- `deptrac.yaml` con capas: `Domain`, `Application`, `Infrastructure`, `App`, `Shared` y reglas "Domain no importa nada hacia fuera; Application puede usar Domain; Infrastructure puede usar Domain y Application; App puede usar todo lo anterior"
- `tests/Unit/Core/Domain/Model/AggregateRootTest.php`
- `tests/Unit/Shared/Domain/Model/ErrorCodeTest.php`
- `tests/Unit/Core/Application/Bus/BusContractTest.php`

**Criterio de salida**: `phpstan` verde, `deptrac` verde, tests unitarios nuevos verdes, app vieja sigue arrancando.

### 🚧 Gate A — merge Fase 0 + Fase 1
Verificaciones obligatorias antes de continuar:
- `make start` arranca
- `make code-quality` verde (phpcs + phpstan + deptrac)
- `make tests-unit` verde
- GHA CI verde en PR de prueba

### Fase 2 — Vertical slices por aggregate (4 lotes secuenciales · 11 agentes en total)

Cada agente trabaja un aggregate completo, end-to-end:

1. **VOs** en `Core/Domain/Model/VO/<Aggregate>/`: como mínimo `<Aggregate>Id` (UUID, valida formato y lanza `Invalid<Aggregate>IdException`); VOs adicionales con invariantes (ej. `UserName`, `ClientName`). VOs comunes (`Email`, `Password`, `Phone`, `Url`) en `Core/Domain/Model/VO/Common/`.
2. **DBAL Types** en `Core/Infrastructure/Persistence/Doctrine/DBAL/Types/`: uno por VO con tipo no nativo. Registrados en `doctrine.yaml`.
3. **Excepciones de dominio** en `Core/Domain/Exception/<Aggregate>/`. Cases del `ErrorCode` ya existen desde Fase 1.
4. **Aggregate rico** en `Core/Domain/Model/Aggregate/<Aggregate>.php`:
   - Extiende `AggregateRoot`
   - Constructor privado (o protected) hidratable por Doctrine
   - Factory público estático `create(...)` que registra `<Aggregate>WasCreated`
   - Mutadores con nombre de negocio (`activate()`, `changeRole(...)`, `assignTo(...)`...) que registran evento `<Aggregate>Was*`
   - Getters explícitos, sin setters genéricos
   - Sin anotaciones Doctrine/Serializer
5. **Eventos** `<Aggregate>WasCreated`, `<Aggregate>WasUpdated`, `<Aggregate>WasDeactivated`, etc. en `Core/Domain/Model/Event/<Aggregate>/`
6. **Repository interface** `<Aggregate>Repository` en `Core/Domain/Model/Repository/`: `add()`, `remove()`, `find(<Aggregate>Id): ?<Aggregate>`, `findOneOrFail(<Aggregate>Id): <Aggregate>` (lanza `<Aggregate>NotFoundException`), métodos especializados
7. **Orm<Aggregate>Repository** en `Core/Infrastructure/Persistence/Doctrine/ORM/`: `final readonly`, inyecta `EntityManagerInterface`
8. **Mapping XML** `<Aggregate>.orm.xml` en `…/Mapping/XML/`: respeta nombres de tabla/columna actuales para cero migración SQL
9. **Domain Services** (uno por acción de escritura): `Core/Domain/Service/<Aggregate>/<Action>/<Action>Service.php` + `<Action>ServiceInterface.php`. `readonly`, único método `__invoke()`. Solo VOs en firma.
10. **Handlers**:
    - Writes: `Core/Application/Command/<Aggregate>/<Action>/<Action>Command.php` (`readonly`, primitivos, primer parámetro `SecurityToken`) + `<Action>Handler.php` (`implements CommandHandler, SecurableHandler`, `use SecurityAwareTrait`)
    - Reads: `Core/Application/Query/<Aggregate>/<Action>/<Action>Query.php` + `<Action>Handler.php` (`implements QueryHandler`)
11. **Controllers** en `App/UI/API/Controller/<Aggregate>/<Action>/`:
    - `<Action>Controller.php` invocable, `#[Route]`, `#[OA\Tag]`, inyecta `CommandBus|QueryBus` + `SecurityTokenExtractorInterface`, sin try/catch, sin `IsGranted`
    - `<Action>Request.php` `final readonly` con `#[Assert\…]`
    - `<Action>Response.php` para queries (`final readonly`)
12. **Mothers** en `tests/Unit/Core/Domain/Mother/<Aggregate>/`: `<Aggregate>IdMother`, `<Aggregate>NameMother`, `<Aggregate>Mother` (todos los parámetros opcionales con defaults Faker)
13. **Tests unitarios**:
    - VOs (válido / inválido por cada invariante)
    - Aggregate (factory + transiciones que registran eventos)
    - Domain Service (mock del repo, camino feliz + ramas de excepción)
    - Handler (mock del service + security checker)
14. **Borra** los archivos del aggregate en su carpeta vieja (`src/<Context>/...`). Si la carpeta del contexto queda vacía, el agente la deja vacía (la Fase 6 la borra).

#### Lotes y dependencias

```
Lote A (5 agentes) — sin dependencias:
  User, Sector, Technology, ProjectRole, RefreshToken

Lote B (2 agentes) — depende de A:
  Client (→ SectorId)
  Contact (→ ClientId)

Lote C (2 agentes) — depende de A+B:
  Project (→ ClientId, UserId, TechnologyId)
  Link (→ ProjectId)   # paralelo a Project; ambos crean VO ProjectId si el otro no lo ha hecho aún, merge trivial

Lote D (2 agentes) — depende de A+B+C:
  ProjectUser (→ ProjectId, UserId, ProjectRoleId)
  TimeEntry (→ ProjectId, UserId)
```

**Mecánica de ejecución**: cada agente trabaja en un **git worktree aislado** (skill `superpowers:dispatching-parallel-agents` con `isolation: worktree`). Tras cada lote se merge a la rama principal con resolución de conflictos en archivos compartidos (esperado: ninguno, porque cada aggregate vive en su propio sub-árbol).

**Coexistencia old/new**: durante Fase 2, el doctrine.yaml mantiene autoloaded sólo `App\Core` (XML). Las carpetas viejas (`UserManagement`, etc.) ya no son detectadas por Doctrine — sus repositorios y handlers están rotos a propósito. Cualquier endpoint NO migrado devuelve 500 hasta que su slice lo cubra.

### 🚧 Gates B, C, D, E — uno por lote
Verificaciones obligatorias:
- `phpstan` verde
- `deptrac` verde
- `php bin/console doctrine:schema:validate` verde
- `make tests-unit` verde
- Endpoints de los aggregates ya migrados responden HTTP manualmente (smoke test con curl al menos un endpoint de creación + lectura por aggregate)

### Fase 3 — Plumbing transversal final (1 agente)

- Suscribir `ExceptionListener` a `kernel.exception` (`services.yaml`)
- Completar `MapperExceptionToJsonErrorResponse` con todos los mapeos:
  - `Invalid*Exception` → HTTP 400
  - `*NotFoundException` → HTTP 404
  - `Duplicated*Exception` → HTTP 409
  - `ForbiddenException` → HTTP 403
  - `UnableToExtractSecurityTokenClaimsException` → HTTP 401
  - Cualquier otra → HTTP 500 con `ErrorCode::UNEXPECTED_ERROR` (oculta detalles en prod)
- Sustituir `PermissiveSecurityChecker` por implementación real basada en `SystemRole` del token (ADMIN/EMPLOYEE) que replica las reglas que hoy están como `IsGranted` en los controllers viejos
- Auditoría: confirmar que no quedan `try/catch` en controllers ni `#[IsGranted]`
- Implementar listener Doctrine `postFlush` en `Core/Infrastructure/Persistence/Doctrine/EventDispatcher.php` que recorre los aggregates marcados como persistidos, llama a `pullEvents()` y despacha cada evento al event bus (Symfony Messenger third bus, `event.bus`)
- Configurar `event.bus` en `messenger.yaml`

**Criterio de salida**: peticiones inválidas devuelven JSON `{code, message}` con HTTP correcto; un Aggregate cualquiera dispara un evento de prueba al persistirse y el subscriber del test lo recibe.

### Fase 4 — Eventos cross-aggregate (N agentes paralelos)

Subscribers conocidos (cada uno = un agente):
- `UserWasDeactivated` subscriber que marca inactivos los `ProjectUser` asociados (sustituye `DoctrineUserRepository::updateUserActivationWithRelation` actual)
- Otros que se identifiquen durante Fase 2 (ej. al borrar un `Sector`, validar que no tiene `Client`s asociados; al desactivar un `Project`, decidir efecto sobre `TimeEntry`s)

Convención: subscribers viven en `Core/Application/EventSubscriber/<Aggregate>/<EventName>Subscriber.php` e implementan `MessageHandlerInterface` (suscrito a `event.bus`).

**Criterio de salida**: tests unitarios de cada subscriber verdes; test E2E del flujo "desactivar usuario propaga a project_user" verde.

### Fase 5 — Tests E2E Codeception (11 agentes paralelos)

Un agente por aggregate, escribe los `*Cest.php` en `tests/Api/<Aggregate>/`.

Por cada endpoint del aggregate (Create/Update/Delete/Get/List/etc.):
- Camino feliz
- 404 (recurso no existe, si aplica)
- 400 (payload inválido — ID mal formado, campo requerido vacío)
- 409 (invariante violada — duplicado, transición no válida — si aplica)
- 403 (usuario sin permisos — si aplica)

Helpers a añadir a `tests/Support/ApiTester.php` (cada agente añade los suyos):
- `with<Aggregate>(...)` que precarga un aggregate en BD via repositorio
- Métodos auxiliares de auth (`haveAdminHttpHeaders()`, `haveEmployeeHttpHeaders()`) ya existen desde Fase 0

Migración BD en CI: extension `DbMigrationExtension` configurada en `codeception.yml` corre `doctrine:migrations:migrate` antes de cada suite Api.

**Criterio de salida**: checklist cap. 09 cumplido para los 11 aggregates; `make tests-api` verde local y en CI.

### Fase 6 — Limpieza final (1 agente)

- Borrar carpetas vacías: `src/{Auth,ClientManagement,ProjectManagement,TimeManagement,UserManagement}/`
- Eliminar `src/Shared/Infrastructure/Http/AppController.php` y cualquier otro residuo si ya no se referencia
- Eliminar imports no usados (`composer dump-autoload`, revisar con phpstan baseline a 0)
- Verificación: `find src -name "*.php" | xargs grep -l 'UserManagement\|ClientManagement\|ProjectManagement\|TimeManagement'` devuelve vacío
- Actualizar `readme.md` con la arquitectura final y cómo añadir una feature (referencia al cap. 04)
- Pasada de revisión cap. 08 (convenciones) y cap. 10 (errores comunes)

**Criterio de salida**: `make code-quality` + `make tests-all` + GHA CI verdes; las búsquedas de strings de carpetas viejas devuelven vacío.

### Diagrama de paralelismo

```
┌─ Fase 0 ┐
│         ├──> Gate A ──┐
└─ Fase 1 ┘             │
                        ├──> Lote A (5p) ──> Gate B ──> Lote B (2p) ──> Gate C
                        │                                                    │
                        │                                                    ▼
                        │                                              Lote C (2p) ──> Gate D
                        │                                                    │
                        │                                                    ▼
                        │                                              Lote D (2p) ──> Gate E
                        │                                                              │
                        └──────────────────────────────────────────────────────────────┴──> Fase 3 ──> Fase 4 (Np) ──> Fase 5 (11p) ──> Fase 6
```

---

## 6. Mapeo aggregate-por-aggregate

Para cada uno: ID, VOs adicionales esperados, eventos clave, comandos/queries, endpoints actuales que se conservan.

### User (Lote A)
- ID: `UserId` (UUID)
- VOs: `Email`, `Password`, `UserName`, `UserSurname`, `SystemRole` (ya enum en Shared, mantener)
- Eventos: `UserWasCreated`, `UserWasUpdated`, `UserWasActivated`, `UserWasDeactivated`, `UserPasswordWasChanged`, `UserRoleWasChanged`
- Acciones: `CreateUser`, `UpdateUser`, `DeleteUser`, `ChangePassword`, `ResetPassword`, `ToggleActivation`, `GetUser`, `ListUser`
- Notas: implementa `UserInterface`+`PasswordAuthenticatedUserInterface` (Symfony Security) — mantener en el Aggregate

### Sector (Lote A)
- ID: `SectorId`
- VOs: `SectorName`
- Eventos: `SectorWasCreated`, `SectorWasUpdated`, `SectorWasDeleted`
- Acciones: `CreateSector`, `UpdateSector`, `DeleteSector`, `GetSector`, `ListSectors`

### Technology (Lote A)
- ID: `TechnologyId`
- VOs: `TechnologyName`
- Eventos: `TechnologyWasCreated`
- Acciones: `ListTechnologies` (otras CRUD según implementación actual)

### ProjectRole (Lote A)
- ID: `ProjectRoleId`
- VOs: `ProjectRoleName`
- Eventos: `ProjectRoleWasCreated`, `ProjectRoleWasDeleted`
- Acciones: `CreateProjectRole`, `DeleteProjectRole`

### RefreshToken (Lote A)
- ID: `RefreshTokenId`
- VOs: `RefreshTokenValue`, `RefreshTokenExpiresAt`
- Eventos: `RefreshTokenWasIssued`, `RefreshTokenWasRevoked`
- Acciones: el `Logout` y `ForceLogout` actuales se convierten en handlers contra este aggregate

### Client (Lote B → depende de Sector)
- ID: `ClientId`
- VOs: `ClientName`, `ClientCIF` (si aplica), `Phone`, `Url`
- Referencias por VO: `SectorId`
- Eventos: `ClientWasCreated`, `ClientWasUpdated`, `ClientWasActivated`, `ClientWasDeactivated`, `ClientWasDeleted`
- Acciones: `CreateClient`, `UpdateClient`, `DeleteClient`, `ToggleClientActivation`, `GetClient`, `ListClients`

### Contact (Lote B → depende de Client)
- ID: `ContactId`
- VOs: `ContactName`, `Email`, `Phone`, `ContactPosition`
- Referencias por VO: `ClientId`
- Eventos: `ContactWasCreated`, `ContactWasUpdated`, `ContactWasMarkedAsMain`, `ContactWasDeleted`
- Acciones: `CreateContact`, `UpdateContact`, `DeleteContact`, `MarkContactAsMain`, `ListContacts`

### Link (Lote B → depende de Project siendo aggregate hermano)
- ID: `LinkId`
- VOs: `LinkUrl` (subtipo de `Url`), `LinkLabel`
- Referencias por VO: `ProjectId`
- Eventos: `LinkWasCreated`, `LinkWasDeleted`
- Acciones: `CreateLink`, `DeleteLink`
- Nota: aunque referencia `ProjectId`, va en Lote B porque solo necesita que el VO `ProjectId` exista — no el aggregate Project completo. El VO `ProjectId` se crea en Lote C *junto* con el aggregate Project, así que Link debe esperar a Lote C o crear su propio `ProjectId` provisional en Lote B. **Decisión**: mover Link a Lote C (depende de Project como VO).

### Project (Lote C → depende de Client, User, Technology)
- ID: `ProjectId`
- VOs: `ProjectName`, `ProjectDescription`, `ProjectStartDate`, `ProjectEndDate`, VOs de Development (estado, fase, etc.)
- Referencias por VO: `ClientId`, `UserId` (manager), `TechnologyId[]`
- Eventos: `ProjectWasCreated`, `ProjectWasUpdated`, `ProjectDevelopmentWasUpdated`
- Acciones: `CreateProject`, `UpdateProject`, `UpdateDevelopment`, `ListProjects`

### ProjectUser (Lote D)
- ID: `ProjectUserId`
- VOs: `Allocation` (porcentaje o horas)
- Referencias por VO: `ProjectId`, `UserId`, `ProjectRoleId`
- Eventos: `UserWasAssignedToProject`, `ProjectUserWasUpdated`, `ProjectUserWasActivated`, `ProjectUserWasDeactivated`
- Acciones: `AssignUserToProject`, `SyncProjectUsers`, `UpdateProjectUser`, `ToggleProjectUserActivation`

### TimeEntry (Lote D)
- ID: `TimeEntryId`
- VOs: `TimeEntryDate`, `TimeEntryHours`, `TimeEntryDescription`
- Referencias por VO: `ProjectId`, `UserId`
- Eventos: `TimeEntryWasCreated`, `TimeEntryWasUpdated`, `TimeEntryWasDeleted`
- Acciones: `CreateTimeEntry`, `UpdateTimeEntry`, `DeleteTimeEntry`, `GetTimeEntry`, `ListTimeEntriesByUser`, `ListTimeEntriesByProject`

### Development — NO es aggregate
Por decisión 16, `Development` se modela como entidad-valor dentro del aggregate `Project`. El controller `UpdateDevelopment` invoca el mutador `Project::updateDevelopment(...)` que registra el evento `ProjectDevelopmentWasUpdated`. No tiene VOs propios, ni repositorio, ni mapping XML independiente — sus columnas viven en la misma tabla `project` o en una tabla satélite mapeada como embedded/composite dentro del XML de Project.

---

## 7. Riesgos y mitigaciones

| Riesgo | Severidad | Mitigación |
|---|---|---|
| Conflictos de merge en `ErrorCode.php` | Media | Pre-rellenar en Fase 1 con todos los cases previsibles; cada agente añade al final si necesita más |
| Conflictos en `composer.json` | Baja | Todos los `composer require` (Codeception, deptrac) en Fase 0/1 |
| Conflictos en `tests/Support/ApiTester.php` (Fase 5) | Media | Cada agente añade solo sus helpers `with<Aggregate>()` (un método nuevo no choca con otros); merge ordenado tras Fase 5 |
| App rota durante Lote A (endpoints viejos sin responder) | Aceptado | Es consecuencia explícita de la estrategia vertical-slice; Gate B exige smoke manual a endpoints del lote ya migrado |
| Doctrine no reconoce mapping XML mientras coexisten atributos viejos | Alta | En Fase 0/1 se configura `doctrine.yaml` para autoloadear SOLO `App\Core` con XML; las entidades viejas dejan de ser detectadas |
| Doble mapping a misma tabla | Alta | Convención: borrar simultáneamente los archivos viejos del aggregate dentro del mismo slice |
| `JwtSecurityTokenExtractor` no extrae bien rol/UUID del JWT actual | Media | Test de integración en Fase 1 con JWT real del proyecto; logueado y fallback a `UNEXPECTED_ERROR` si falla |
| Tests E2E descubren regresiones tarde | Media | Cada slice de Fase 2 incluye smoke manual + unit del handler; Fase 5 es validación de contrato HTTP, no de funcionalidad nueva |
| Frontend rompe por cambios de contrato HTTP | Baja | Restricción dura: URLs, payloads, status codes NO cambian; Fase 5 verifica el contrato |
| Estilo divergente entre agentes paralelos | Baja | `phpcs` ejecutado en cada gate; el prompt de cada agente incluye el resumen del cap. 08 (apéndice de este spec) |
| Migraciones de Doctrine añadidas accidentalmente | Media | `doctrine:schema:validate` debe estar verde en cada gate; cualquier diff inesperado se investiga antes de seguir |
| Permisos de FrankenPHP con volumes en host Linux/WSL | Baja | `Dockerfile` recibe `APP_USER_ID`/`APP_GROUP_ID` como build-args; el Makefile los inyecta desde `id -u`/`id -g` |

---

## 8. Apéndice: convenciones del cap. 08 condensadas

Para incluir en el prompt de cada agente paralelo de Fase 2:

- `declare(strict_types=1);` en todo archivo PHP
- Clases `final` cuando no se hereden; aggregates pueden NO ser final si Doctrine necesita proxies
- `readonly` en Commands, Queries, DTOs, Domain Services, Repositorios Doctrine
- Propiedades promovidas en constructor (PHP 8.1+)
- Getters explícitos, nunca `__get`
- Nombre de métodos = verbo de negocio (`activate()`, `assignTo()`, no `setIsActive(true)`)
- Anotaciones `@throws` en cada método que pueda lanzar excepción de dominio
- `array<Type>` en docblocks para arrays tipados (PHPStan-compatible)
- Test names: `test_Given{X}_When{Y}_Then{Z}` snake_case (excepción permitida al PSR-12 en tests)
- Mothers obligatorios para VOs y Aggregates en tests
- En `OrmRepository`: inyectar `EntityManagerInterface`, jamás extender `ServiceEntityRepository`
- Excepciones de dominio: heredan de `CustomException`, mensaje `TR_*`, `ErrorCode` propio, `messageParams` para placeholders

---

## 9. Lo que NO entra en este plan

- Cambios de schema de BD (no se renombran tablas/columnas)
- Reescribir el frontend ni cambiar el contrato HTTP (URLs, payloads, status codes)
- Tocar lógica de negocio existente (bugs detectados se anotan como deuda separada)
- Optimizaciones de rendimiento
- Despliegue (Helm/EKS del ejemplo NO se replica)
- Zitadel / OIDC / SSO (mantenemos lexik JWT)
- RabbitMQ + Messenger asíncrono (todo síncrono)
- Supervisor + workers
- pgvector
- Multi-tenant
- Multi-entity-manager
- MailDev (puede añadirse después si hace falta)

---

## 10. Próximo paso

Con este spec aprobado, se invoca la skill `superpowers:writing-plans` para generar el **plan de implementación** detallado, con la lista exacta de tareas, archivos a crear/modificar, comandos a ejecutar y checkpoints de revisión por fase. Ese plan será el input directo de los agentes paralelos en Fase 2 y siguientes.
