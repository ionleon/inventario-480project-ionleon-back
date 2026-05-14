# Plan 5 — Lote D — Slices verticales (ProjectUser, TimeEntry)

> **For agentic workers:** REQUIRED SUB-SKILL: superpowers:subagent-driven-development. 2 agentes en paralelo. Aplican `docs/superpowers/plans/recipe-aggregate-slice.md`.

**Goal:** Migrar ProjectUser (→Project, User, ProjectRole) y TimeEntry (→Project, User) — los aggregates con más dependencias.

**Prerequisitos:** Plan 4 mergeado (`plan-04-complete`).

---

## ⚠️ ACTUALIZACIÓN (lección de la primera ejecución del Plan 2)

Las secciones **"### Borrado legacy"** de cada slice están **OBSOLETAS**. Ver banner en Plan 02 y `recipe-aggregate-slice.md` § Paso 14.

**Nueva estrategia:** los slices solo CREAN código nuevo; comentan `#[Route]` y `#[ORM\Entity]` del legacy correspondiente; NO borran archivos. Borrado físico → Plan 8.

---

## Ejecución paralela

```bash
git worktree add ../wt-projectuser -b ddd/projectuser feature/ddd-refactor
git worktree add ../wt-timeentry   -b ddd/timeentry   feature/ddd-refactor
```

---

## Slice 1 — ProjectUser

**Tabla**: `project_user`.
**Contexto legacy**: `src/ProjectManagement/.../ProjectUser*`, controllers `*ProjectUser*`, `AssignUserToProjectController`, `SyncProjectUsersController`, `ToggleProjectUserActivationController`.

### VOs

| VO | Reglas |
|---|---|
| `ProjectUserId` | UUID |
| `ProjectUserAllocation` | int 0..100 (porcentaje) o DecimalHours (decidir mirando legacy) |

### Referencias por VO

- `ProjectId`, `UserId`, `ProjectRoleId`

### Eventos

- `UserWasAssignedToProject(ProjectUserId, ProjectId, UserId, ProjectRoleId)`
- `ProjectUserWasUpdated(ProjectUserId)`
- `ProjectUserWasActivated(ProjectUserId)` / `ProjectUserWasDeactivated(ProjectUserId)`
- `ProjectUserWasRemoved(ProjectUserId, ProjectId, UserId)` (para Sync)

### Acciones

| Acción | Tipo | Ruta |
|---|---|---|
| `AssignUserToProject` | Command | `POST /projects/{projectId}/users` |
| `SyncProjectUsers` | Command | `PUT /projects/{projectId}/users` (lista completa, sustituye) |
| `UpdateProjectUser` | Command | `PUT /project-users/{id}` (cambia rol/allocation) |
| `ToggleProjectUserActivation` | Command | `POST /project-users/{id}/toggle-activation` |
| `ListProjectUsers` | Query | `GET /projects/{projectId}/users` |

### Aggregate

```php
class ProjectUser extends AggregateRoot
{
    private function __construct(
        private readonly ProjectUserId $id,
        private readonly ProjectId $projectId,
        private readonly UserId $userId,
        private ProjectRoleId $roleId,
        private ProjectUserAllocation $allocation,
        private bool $isActive,
        private readonly DateTimeImmutable $assignedAt,
    ) {}

    public static function assign(
        ProjectUserId $id, ProjectId $projectId, UserId $userId,
        ProjectRoleId $roleId, ProjectUserAllocation $allocation,
    ): self {
        $instance = new self($id, $projectId, $userId, $roleId, $allocation, true, new DateTimeImmutable());
        $instance->recordEvent(UserWasAssignedToProject::from($instance));
        return $instance;
    }

    public function update(ProjectRoleId $roleId, ProjectUserAllocation $allocation): void
    {
        if ($this->roleId->equals($roleId) && $this->allocation->equals($allocation)) {
            return;
        }
        $this->roleId = $roleId;
        $this->allocation = $allocation;
        $this->recordEvent(ProjectUserWasUpdated::from($this));
    }

    public function activate(): void
    {
        if ($this->isActive) return;
        $this->isActive = true;
        $this->recordEvent(ProjectUserWasActivated::from($this));
    }

    public function deactivate(): void
    {
        if (!$this->isActive) return;
        $this->isActive = false;
        $this->recordEvent(ProjectUserWasDeactivated::from($this));
    }

    public function toggleActivation(): void { $this->isActive ? $this->deactivate() : $this->activate(); }
}
```

### Domain Services

- `AssignUserToProjectService`: valida que `(projectId, userId)` no exista ya activo. Si existe inactivo: lo reactiva (con `Updated` event). Si no existe: crea.
- `SyncProjectUsersService`:
  - Recibe `ProjectId` + `list<{userId, roleId, allocation}>`.
  - Carga los `ProjectUser`s existentes del proyecto.
  - Determina diffs: a crear, a actualizar, a desactivar (los que no están en la lista nueva).
  - Aplica cambios.
  - Los `recordEvent` ocurren dentro del aggregate.
- `ToggleProjectUserActivationService`: invoca `toggleActivation()` del aggregate.

### Reactividad a `UserWasDeactivated`

> **Importante**: la regla "al desactivar un User, todos sus ProjectUser quedan inactivos" se implementa como **subscriber del evento `UserWasDeactivated`** en Plan 6 (no en este slice). Aquí solo dejamos el aggregate con su mutador `deactivate()` disponible para que el subscriber lo invoque.

### Borrado legacy

```bash
git rm -r src/ProjectManagement/Domain/ProjectUser src/ProjectManagement/Infrastructure/ProjectUser
git rm src/ProjectManagement/Application/{AssignUserToProject,SyncProjectUsers,UpdateProjectUser,ToggleProjectUserActivation}/
git rm src/ProjectManagement/Infrastructure/Http/{AssignUserToProject,SyncProjectUsers,UpdateProjectUser,ToggleProjectUserActivation}Controller.php
```

---

## Slice 2 — TimeEntry

**Tabla**: `time_entry`.
**Contexto legacy**: `src/TimeManagement/Domain/TimeEntry*`, `src/TimeManagement/Application/*`, `src/TimeManagement/Infrastructure/TimeEntry/`, controllers correspondientes.

### VOs

| VO | Reglas |
|---|---|
| `TimeEntryId` | UUID |
| `TimeEntryDate` | DateTimeImmutable solo fecha (truncar hora) |
| `TimeEntryHours` | decimal > 0 y <= 24 |
| `TimeEntryDescription` | max 500, nullable |

### Referencias por VO

- `ProjectId`, `UserId`

### Eventos

- `TimeEntryWasCreated(TimeEntryId, UserId, ProjectId, TimeEntryDate, TimeEntryHours)`
- `TimeEntryWasUpdated(TimeEntryId)`
- `TimeEntryWasDeleted(TimeEntryId)`

### Acciones

| Acción | Tipo | Ruta |
|---|---|---|
| `CreateTimeEntry` | Command | `POST /time-entries` |
| `UpdateTimeEntry` | Command | `PUT /time-entries/{id}` |
| `DeleteTimeEntry` | Command | `DELETE /time-entries/{id}` |
| `GetTimeEntry` | Query | `GET /time-entries/{id}` |
| `ListTimeEntriesByUser` | Query | `GET /users/{userId}/time-entries` |
| `ListTimeEntriesByProject` | Query | `GET /projects/{projectId}/time-entries` |

### Aggregate

```php
class TimeEntry extends AggregateRoot
{
    private function __construct(
        private readonly TimeEntryId $id,
        private readonly UserId $userId,
        private readonly ProjectId $projectId,
        private TimeEntryDate $date,
        private TimeEntryHours $hours,
        private ?TimeEntryDescription $description,
        private readonly DateTimeImmutable $createdAt,
    ) {}

    public static function create(
        TimeEntryId $id, UserId $userId, ProjectId $projectId,
        TimeEntryDate $date, TimeEntryHours $hours, ?TimeEntryDescription $description = null,
    ): self {
        $instance = new self($id, $userId, $projectId, $date, $hours, $description, new DateTimeImmutable());
        $instance->recordEvent(TimeEntryWasCreated::from($instance));
        return $instance;
    }

    public function update(TimeEntryDate $date, TimeEntryHours $hours, ?TimeEntryDescription $description): void
    {
        $this->date = $date; $this->hours = $hours; $this->description = $description;
        $this->recordEvent(TimeEntryWasUpdated::from($this));
    }
}
```

### Domain Services

- `CreateTimeEntryService`: valida que User existe, Project existe, User está activo en Project (consulta `ProjectUserRepository::findActiveByUserAndProject`). Lanza `UserNotAssignedToProjectException` si no.
- `UpdateTimeEntryService`: idem.

### Borrado legacy

```bash
git rm -r src/TimeManagement
```

(Si la carpeta `src/TimeManagement` queda vacía tras borrar todos los archivos, eliminarla.)

---

## 🚧 Gate E — Verificación tras merge

```bash
git checkout feature/ddd-refactor
git merge --no-ff ddd/projectuser
git merge --no-ff ddd/timeentry
```

- [ ] `make composer ARGS="phpstan deptrac"` verde
- [ ] `make bin-console ARGS="doctrine:schema:validate"` verde
- [ ] `make tests-unit` verde
- [ ] Smoke E2E completo: login + crear sector + crear client + crear user + crear project + asignar user al project + crear time entry. Todo encadenado debe responder.
- [ ] CI verde

```bash
git push origin feature/ddd-refactor
git tag -a plan-05-complete -m "Plan 5: Lote D (ProjectUser + TimeEntry) completados (Gate E). Todos los aggregates migrados."
git push origin plan-05-complete
```

### Estado tras Gate E

- Los **11 aggregates** están migrados.
- TODOS los endpoints HTTP responden con el nuevo flujo.
- El `PermissiveSecurityChecker` sigue permitiendo todo.
- El `ExceptionListener` sigue desconectado (las excepciones de dominio se propagan como 500).
- Subscribers cross-aggregate aún no existen (el cleanup de `UserWasDeactivated → ProjectUser` está pendiente).

---

## Siguiente plan

Plan 6: **Plumbing transversal final + eventos cross-aggregate**. Documento: `2026-05-14-06-plumbing-y-eventos.md`.
