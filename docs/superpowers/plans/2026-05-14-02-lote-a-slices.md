# Plan 2 — Lote A — Slices verticales (User, Sector, Technology, ProjectRole, RefreshToken)

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development. Dispatch UN agente por aggregate en paralelo en git worktrees aislados (`isolation: worktree`). Cada agente sigue `docs/superpowers/plans/recipe-aggregate-slice.md` aplicada a su aggregate concreto.

**Goal:** Migrar los 5 aggregates sin dependencias (User, Sector, Technology, ProjectRole, RefreshToken) a la nueva arquitectura DDD/CQRS, end-to-end en su slice vertical.

**Architecture:** 5 slices verticales independientes. Cada slice aplica la receta canónica (`recipe-aggregate-slice.md`) con sus parámetros específicos. Tras merge se ejecuta Gate B.

**Tech Stack:** Heredado de Plan 1 (FrankenPHP, Symfony Messenger, Codeception, deptrac).

**Prerequisitos:** Plan 1 completado y mergeado (tag `plan-01-complete`).

---

## ⚠️ ACTUALIZACIÓN (lección de la primera ejecución)

Las secciones tituladas **"### Borrado legacy"** dentro de cada slice de este documento están **OBSOLETAS** y deben ser ignoradas. Borrar el legacy aggregate-por-aggregate durante los slices rompe las dependencias cross-context del código legacy (ej. Client legacy importa Sector legacy; al borrar Sector legacy mientras Client legacy aún existe, el código deja de compilar).

**Nueva estrategia:**
- Cada slice **solo CREA código nuevo** en `src/Core/...`, `src/App/UI/...` y configs.
- Cada slice **comenta** los `#[Route(...)]` de los controllers legacy de SU aggregate (sin borrar el archivo) para evitar colisión con las nuevas rutas.
- Cada slice **quita `#[ORM\Entity]`** de la entidad legacy de SU aggregate (sin borrar el archivo) para evitar doble mapping con la nueva XML.
- El **borrado físico** de las carpetas legacy se hace **completo y atómico** en **Plan 8 (cleanup)**, cuando los 11 aggregates están migrados y nadie referencia legacy.

Ver `docs/superpowers/plans/recipe-aggregate-slice.md` § Paso 14 para el detalle del nuevo procedimiento.

---

## Ejecución paralela

```bash
# Desde la rama feature/ddd-refactor en estado limpio
git worktree add ../wt-user      -b ddd/user      feature/ddd-refactor
git worktree add ../wt-sector    -b ddd/sector    feature/ddd-refactor
git worktree add ../wt-tech      -b ddd/tech      feature/ddd-refactor
git worktree add ../wt-projrole  -b ddd/projrole  feature/ddd-refactor
git worktree add ../wt-refresh   -b ddd/refresh   feature/ddd-refactor
```

Cada agente trabaja en su worktree. Al finalizar TODOS los 5, merge a `feature/ddd-refactor` con `git merge --no-ff ddd/<x>`.

> **Conflictos esperados**: ninguno en archivos de código (cada aggregate vive en sub-árbol propio). Posible merge trivial en `config/services.yaml` (cada slice añade bind del repo) y `config/packages/doctrine.yaml` (cada slice registra DBAL types). Estos archivos tienen secciones bien delimitadas: el merge es por concatenación.

---

## Slice 1 — User

**Tabla**: revisar `migrations/`; probablemente `app_user`.
**Contexto legacy**: `src/UserManagement/`.
**Caracterizado por**: implementa `Symfony\Component\Security\Core\User\UserInterface` y `PasswordAuthenticatedUserInterface` (login + JWT dependen de esto).

### VOs

| VO | Reglas |
|---|---|
| `UserId` | UUID válido |
| `Email` (en `Common/`) | Formato RFC válido, lowercase canónico, max 150 |
| `Password` (en `Common/`) | Min 8 chars; constructor recibe la versión **hasheada**; método `verify(string $plain, PasswordHasherInterface $hasher)` separado |
| `UserName` | min 3, max 100, trim |
| `UserSurname` | min 1, max 100, trim |

### Eventos

- `UserWasCreated(UserId, Email, SystemRole)`
- `UserWasUpdated(UserId)`
- `UserWasActivated(UserId)` / `UserWasDeactivated(UserId)`
- `UserPasswordWasChanged(UserId)`
- `UserRoleWasChanged(UserId, SystemRole oldRole, SystemRole newRole)`

### Acciones

| Acción | Tipo | Ruta | Notas |
|---|---|---|---|
| `CreateUser` | Command | `POST /users` | Hashea password, valida email único |
| `UpdateUser` | Command | `PUT /users/{id}` | Solo name/surname/role |
| `DeleteUser` | Command | `DELETE /users/{id}` | |
| `ChangePassword` | Command | `POST /users/{id}/password` | Requiere old + new |
| `ResetPassword` | Command | `POST /users/{id}/reset-password` | Genera password temporal, marca `firstTime=true` |
| `ToggleActivation` | Command | `POST /users/{id}/toggle-activation` | Dispara `UserWasDeactivated` que en Plan 6 actualizará `ProjectUser`s |
| `GetUser` | Query | `GET /users/{id}` | |
| `ListUser` | Query | `GET /users` | Paginado, filtros por role/active/term |

### Aggregate específico

`src/Core/Domain/Model/Aggregate/User.php` debe:

```php
class User extends AggregateRoot implements UserInterface, PasswordAuthenticatedUserInterface
{
    private function __construct(
        private readonly UserId $id,
        private Email $email,
        private UserName $name,
        private UserSurname $surname,
        private Password $password,
        private SystemRole $role,
        private bool $isActive,
        private bool $firstTime,
        private readonly DateTimeImmutable $createdAt,
    ) {}

    public static function create(
        UserId $id,
        Email $email,
        UserName $name,
        UserSurname $surname,
        Password $password,
        SystemRole $role = SystemRole::EMPLOYEE,
    ): self {
        $instance = new self($id, $email, $name, $surname, $password, $role, true, true, new DateTimeImmutable());
        $instance->recordEvent(UserWasCreated::from($instance));
        return $instance;
    }

    public function getUserIdentifier(): string { return (string) $this->email; }
    public function getRoles(): array { return [$this->role->value]; }
    public function getPassword(): ?string { return (string) $this->password; }
    public function eraseCredentials(): void {}

    public function changePassword(Password $newPassword): void { $this->password = $newPassword; $this->firstTime = false; $this->recordEvent(UserPasswordWasChanged::from($this)); }
    public function activate(): void { if ($this->isActive) return; $this->isActive = true; $this->recordEvent(UserWasActivated::from($this)); }
    public function deactivate(): void { if (!$this->isActive) return; $this->isActive = false; $this->recordEvent(UserWasDeactivated::from($this)); }
    public function toggleActivation(): void { $this->isActive ? $this->deactivate() : $this->activate(); }
    public function updateProfile(UserName $name, UserSurname $surname): void { $this->name = $name; $this->surname = $surname; $this->recordEvent(UserWasUpdated::from($this)); }
    public function changeRole(SystemRole $role): void { if ($this->role === $role) return; $old = $this->role; $this->role = $role; $this->recordEvent(new UserRoleWasChanged($this->id, $old, $role, new DateTimeImmutable())); }
    // getters: id(), email(), name(), surname(), role(), isActive(), firstTime(), createdAt()
}
```

### Domain Services específicos

- `CreateUserService` recibe `Password $hashedPassword` (el handler lo hashea antes); valida que no exista otro con el mismo `Email`. Lanza `DuplicatedUserEmailException`.
- `ChangePasswordService` valida el old password contra el aggregate antes de cambiar.

### Tabla `app_user` — XML mapping

Columnas que probablemente existen (validar con `migrations/`):
- `id` (uuid), `email` (varchar 150 unique), `name`, `surname`, `password`, `role` (string-enum), `is_active` (bool), `first_time` (bool), `created_at` (datetime).

### Sustitución del JwtSecurityTokenExtractor

En `src/App/Auth/Infrastructure/JwtSecurityTokenExtractor.php`, cambiar:
```php
use App\UserManagement\Domain\AppUser;
```
por:
```php
use App\Core\Domain\Model\Aggregate\User as AppUser;
```

(O re-importar directo y cambiar todas las referencias internas.)

### Borrado legacy

```bash
git rm -r src/UserManagement
```

Pero **antes** mover `UserFilters` (DTO usado por el repositorio paginado) a `Core/Application/DTO/UserFilters.php` con namespace actualizado y reusarlo en `ListUserHandler`.

### Verificación

```bash
make tests-unit
curl -X POST http://localhost/480project/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password1234"}'
# Expected: 200 con token (login sigue funcionando)

curl -X POST http://localhost/users \
  -H "Authorization: Bearer $TOKEN" -H "Content-Type: application/json" \
  -d '{"id":"...","email":"test@example.com","password":"password1234","name":"Test","surname":"User","role":"ROLE_EMPLOYEE"}'
# Expected: 201
```

---

## Slice 2 — Sector

**Tabla**: revisar `migrations/`; probablemente `sector`.
**Contexto legacy**: `src/ClientManagement/Domain/Sector/`, `src/ClientManagement/Infrastructure/Sector/`.

### VOs

| VO | Reglas |
|---|---|
| `SectorId` | UUID |
| `SectorName` | min 2, max 100, único en BD |

### Eventos

- `SectorWasCreated(SectorId, SectorName)`
- `SectorWasUpdated(SectorId)`
- `SectorWasDeleted(SectorId)`

### Acciones

| Acción | Tipo | Ruta |
|---|---|---|
| `CreateSector` | Command | `POST /sectors` |
| `UpdateSector` | Command | `PUT /sectors/{id}` |
| `DeleteSector` | Command | `DELETE /sectors/{id}` |
| `GetSector` | Query | `GET /sectors/{id}` |
| `ListSectors` | Query | `GET /sectors` |

### Aggregate

Simple: `id`, `name`, `createdAt`. Mutador `rename(SectorName)`.

### Reglas especiales

- En `DeleteSector`: si existen `Client`s con este sector → bloquear (Plan 4 al migrar Client confirmará la regla; por ahora basta con que el dominio no haga la validación cross-aggregate aquí).

### Borrado legacy

```bash
git rm -r src/ClientManagement/Domain/Sector src/ClientManagement/Infrastructure/Sector
git rm src/ClientManagement/Application/{Create,Update,Delete,Get,List}Sector*
git rm src/ClientManagement/Infrastructure/Http/{Create,Update,Delete,Get,List}Sector*Controller.php
```

---

## Slice 3 — Technology

**Tabla**: `technology`.
**Contexto legacy**: `src/ProjectManagement/.../Technology*`.

### VOs

| VO | Reglas |
|---|---|
| `TechnologyId` | UUID |
| `TechnologyName` | min 2, max 80, único |

### Eventos

- `TechnologyWasCreated(TechnologyId, TechnologyName)`

### Acciones

Mínimas — el código actual solo tiene `ListTechnologies`. Implementar también `CreateTechnology` y `DeleteTechnology` (CRUD básico) para tener un slice completo aunque no estén expuestas todavía.

| Acción | Tipo | Ruta |
|---|---|---|
| `CreateTechnology` | Command | `POST /technologies` |
| `DeleteTechnology` | Command | `DELETE /technologies/{id}` |
| `ListTechnologies` | Query | `GET /technologies` |

### Borrado legacy

```bash
git rm src/ProjectManagement/Application/ListTechnologies/* \
       src/ProjectManagement/Infrastructure/Http/ListTechnologiesController.php
# (y entidad / repo si están separadas)
```

---

## Slice 4 — ProjectRole

**Tabla**: `project_role`.
**Contexto legacy**: `src/ProjectManagement/.../ProjectRole*`.

### VOs

| VO | Reglas |
|---|---|
| `ProjectRoleId` | UUID |
| `ProjectRoleName` | min 2, max 80, único |

### Eventos

- `ProjectRoleWasCreated`
- `ProjectRoleWasDeleted`

### Acciones

| Acción | Tipo | Ruta |
|---|---|---|
| `CreateProjectRole` | Command | `POST /project-roles` |
| `DeleteProjectRole` | Command | `DELETE /project-roles/{id}` |
| `ListProjectRoles` | Query | `GET /project-roles` |

### Borrado legacy

```bash
git rm -r src/ProjectManagement/Application/{Create,Delete}ProjectRole/ \
          src/ProjectManagement/Infrastructure/Http/{Create,Delete}ProjectRoleController.php
```

---

## Slice 5 — RefreshToken

**Tabla**: `refresh_tokens` (revisar — viene del `gesdinet/jwt-refresh-token-bundle`).
**Contexto legacy**: `src/Auth/Domain/RefreshToken/`, `src/Auth/Infrastructure/Persistence/`.

### Particularidad

El bundle `gesdinet/jwt-refresh-token-bundle` espera una entidad que implemente `RefreshTokenInterface`. Nuestro aggregate `RefreshToken` debe seguir implementando esa interfaz para que el bundle siga funcionando.

### VOs

| VO | Reglas |
|---|---|
| `RefreshTokenId` | UUID o int auto (revisar bundle) |
| `RefreshTokenValue` | string no vacío |
| `RefreshTokenExpiresAt` | DateTimeImmutable futuro al crear |

### Eventos

- `RefreshTokenWasIssued(RefreshTokenId, UserId, RefreshTokenExpiresAt)`
- `RefreshTokenWasRevoked(RefreshTokenId)`

### Aggregate

```php
class RefreshToken extends AggregateRoot implements RefreshTokenInterface
{
    private function __construct(
        private readonly RefreshTokenId $id,
        private RefreshTokenValue $value,
        private string $username,
        private RefreshTokenExpiresAt $expiresAt,
    ) {}

    public static function issue(RefreshTokenId $id, RefreshTokenValue $value, string $username, RefreshTokenExpiresAt $expiresAt): self
    {
        $instance = new self($id, $value, $username, $expiresAt);
        $instance->recordEvent(new RefreshTokenWasIssued($id, $username, $expiresAt, new DateTimeImmutable()));
        return $instance;
    }

    // Métodos requeridos por RefreshTokenInterface (bundle):
    public function getRefreshToken(): string { return (string) $this->value; }
    public function getUsername(): string { return $this->username; }
    public function getValid(): \DateTimeInterface { return $this->expiresAt->value(); }
    public function setRefreshToken(?string $refreshToken = null): self { /* setter requerido por interface */ }
    public function setUsername(string $username): self { /* idem */ }
    public function setValid(\DateTimeInterface $valid): self { /* idem */ }
    public function isValid(): bool { return $this->expiresAt->isInFuture(); }
    public function __toString(): string { return (string) $this->value; }
}
```

> Las firmas `setX` del interface se mantienen como **no-ops mutables** o lanzan excepción (el aggregate prefiere mutadores con nombre, pero el bundle exige la interfaz). Documentar la deuda en el aggregate.

### Acciones

| Acción | Tipo | Ruta |
|---|---|---|
| `Logout` | Command | `POST /logout` (existente) — revoca el RefreshToken del usuario actual |
| `ForceLogout` | Command | `POST /users/{id}/force-logout` — admin revoca todos los RT de un user |

### Reconfiguración del bundle

En `config/packages/gesdinet_jwt_refresh_token.yaml`:

```yaml
gesdinet_jwt_refresh_token:
    refresh_token_class: App\Core\Domain\Model\Aggregate\RefreshToken
    object_manager: doctrine.orm.entity_manager
```

### Verificación

- `curl -X POST http://localhost/480project/refresh-token` sigue funcionando con un refresh válido.
- `Logout` revoca correctamente.

### Borrado legacy

```bash
git rm -r src/Auth/Domain/RefreshToken src/Auth/Infrastructure/Persistence
```

(Mantener `src/Auth/Infrastructure/Controller/` por ahora — esos controllers pasan a `App/UI/API/Controller/Auth/` en el slice; o los movemos en este slice también.)

---

## 🚧 Gate B — Verificación tras merge de los 5 slices

### Task: merge sequence

```bash
git checkout feature/ddd-refactor
git merge --no-ff ddd/user
git merge --no-ff ddd/sector
git merge --no-ff ddd/tech
git merge --no-ff ddd/projrole
git merge --no-ff ddd/refresh
```

Resolver conflictos triviales en `config/services.yaml` y `config/packages/doctrine.yaml` (cada slice añade su bind/type; mantener todos).

### Task: verificaciones de Gate B

- [ ] **Step 1: phpstan + deptrac verdes**

```bash
make composer ARGS="phpstan"
make composer ARGS="deptrac"
```

- [ ] **Step 2: doctrine validation**

```bash
make bin-console ARGS="doctrine:schema:validate"
```

Expected: mappings OK, schema in sync.

- [ ] **Step 3: tests verdes**

```bash
make tests-unit
make tests-functional
```

- [ ] **Step 4: Smoke manual por aggregate**

```bash
# Login sigue funcionando
curl -X POST http://localhost/480project/login -H "Content-Type: application/json" -d '{"email":"admin@example.com","password":"password1234"}'

# CRUD User
curl -X POST http://localhost/users -H "Authorization: Bearer $T" ...
curl -X GET  http://localhost/users -H "Authorization: Bearer $T"

# CRUD Sector
curl -X POST http://localhost/sectors -H "Authorization: Bearer $T" ...

# List Technology
curl -X GET http://localhost/technologies -H "Authorization: Bearer $T"

# CRUD ProjectRole
curl -X POST http://localhost/project-roles -H "Authorization: Bearer $T" ...

# Refresh token
curl -X POST http://localhost/480project/refresh-token -H "Content-Type: application/json" -d '{"refresh_token":"..."}'
```

Cada endpoint del Lote A debe responder con su status HTTP esperado.

- [ ] **Step 5: CI verde**

```bash
git push origin feature/ddd-refactor
```

Verificar GitHub Actions verde.

- [ ] **Step 6: Tag**

```bash
git tag -a plan-02-complete -m "Plan 2: Lote A (5 aggregates) completados (Gate B)"
git push origin plan-02-complete
```

### Estado tras Gate B

- **Endpoints funcionales**: login + CRUD User + CRUD Sector + ListTechnologies + ProjectRoles + RefreshToken.
- **Endpoints rotos (intencionalmente)**: TODO lo de Client, Contact, Project, ProjectUser, TimeEntry, Link, Development (devuelven 500 o 404). Se desbloquea en Plans 3-5.

---

## Siguiente plan

Plan 3: **Lote B — Slices Client y Contact**. Documento: `2026-05-14-03-lote-b-slices.md`.
