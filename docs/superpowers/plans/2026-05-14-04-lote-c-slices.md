# Plan 4 — Lote C — Slices verticales (Project, Link)

> **For agentic workers:** REQUIRED SUB-SKILL: superpowers:subagent-driven-development. 2 agentes en paralelo. Cada uno aplica `docs/superpowers/plans/recipe-aggregate-slice.md` con sus parámetros.

**Goal:** Migrar Project (→Client, User, Technology) y Link (→Project) al modelo DDD/CQRS. El concepto de "Development" entra como entidad-valor dentro del aggregate Project.

**Prerequisitos:** Plan 3 mergeado (`plan-03-complete`).

---

## ⚠️ ACTUALIZACIÓN (lección de la primera ejecución del Plan 2)

Las secciones **"### Borrado legacy"** de cada slice están **OBSOLETAS**. Ver banner en Plan 02 y `recipe-aggregate-slice.md` § Paso 14.

**Nueva estrategia:** los slices solo CREAN código nuevo; comentan `#[Route]` y `#[ORM\Entity]` del legacy correspondiente; NO borran archivos. Borrado físico → Plan 8.

---

## Ejecución paralela

```bash
git worktree add ../wt-project -b ddd/project feature/ddd-refactor
git worktree add ../wt-link    -b ddd/link    feature/ddd-refactor
```

**Coordinación**: ambos slices crean el VO `ProjectId`. Convención: el slice de Project crea el VO primero; el slice de Link, si llega antes al merge, también lo crea — el conflicto resultante es trivial (mismo archivo idéntico). Recomendación: ejecutar Project ligeramente antes y el agente de Link toma `ProjectId` ya creado.

---

## Slice 1 — Project

**Tabla**: `project`.
**Contexto legacy**: `src/ProjectManagement/Domain/Project*`, `src/ProjectManagement/Application/{Create,Update,List,UpdateDevelopment}Project*`, controllers `*Project*`.

### VOs del aggregate Project

| VO | Reglas |
|---|---|
| `ProjectId` | UUID |
| `ProjectName` | min 2, max 200, único |
| `ProjectDescription` | max 2000, nullable |
| `ProjectStartDate` | DateTimeImmutable |
| `ProjectEndDate` | nullable, si presente debe ser >= startDate |

### VOs de Development (embebidos en Project, no aggregate aparte)

| VO | Reglas |
|---|---|
| `DevelopmentStatus` | Enum: `PLANNED`, `IN_PROGRESS`, `BLOCKED`, `COMPLETED` |
| `DevelopmentNotes` | max 2000, nullable |
| `DevelopmentProgress` | int 0..100 |

(Adaptar a las columnas reales — mirar la entidad legacy `Development.php`.)

### Referencias por VO

- `ClientId`
- `UserId` (manager principal)
- `TechnologyId[]` (many-to-many)

### Eventos

- `ProjectWasCreated(ProjectId, ProjectName, ClientId, UserId)`
- `ProjectWasUpdated(ProjectId)`
- `ProjectDevelopmentWasUpdated(ProjectId, DevelopmentStatus, DevelopmentProgress)`

### Acciones

| Acción | Tipo | Ruta |
|---|---|---|
| `CreateProject` | Command | `POST /projects` |
| `UpdateProject` | Command | `PUT /projects/{id}` |
| `UpdateDevelopment` | Command | `POST /projects/{id}/development` — muta el aggregate Project |
| `ListProjects` | Query | `GET /projects` |
| `GetProject` | Query | `GET /projects/{id}` |

### Aggregate

```php
class Project extends AggregateRoot
{
    /** @var list<TechnologyId> */
    private array $technologyIds;

    private function __construct(
        private readonly ProjectId $id,
        private ProjectName $name,
        private ?ProjectDescription $description,
        private ClientId $clientId,
        private UserId $managerId,
        array $technologyIds,
        private ProjectStartDate $startDate,
        private ?ProjectEndDate $endDate,
        // Development embebido
        private DevelopmentStatus $developmentStatus,
        private ?DevelopmentNotes $developmentNotes,
        private DevelopmentProgress $developmentProgress,
        private readonly DateTimeImmutable $createdAt,
    ) {
        $this->technologyIds = array_values($technologyIds);
    }

    /** @param list<TechnologyId> $technologyIds */
    public static function create(
        ProjectId $id, ProjectName $name, ?ProjectDescription $description,
        ClientId $clientId, UserId $managerId, array $technologyIds,
        ProjectStartDate $startDate, ?ProjectEndDate $endDate = null,
    ): self {
        if ($endDate !== null && $endDate->isBefore($startDate)) {
            throw new InvalidProjectDateRangeException();
        }
        $instance = new self(
            $id, $name, $description, $clientId, $managerId, $technologyIds, $startDate, $endDate,
            DevelopmentStatus::PLANNED, null, new DevelopmentProgress(0),
            new DateTimeImmutable(),
        );
        $instance->recordEvent(ProjectWasCreated::from($instance));
        return $instance;
    }

    /** @param list<TechnologyId> $technologyIds */
    public function update(
        ProjectName $name, ?ProjectDescription $description, ClientId $clientId, UserId $managerId,
        array $technologyIds, ProjectStartDate $startDate, ?ProjectEndDate $endDate,
    ): void {
        if ($endDate !== null && $endDate->isBefore($startDate)) {
            throw new InvalidProjectDateRangeException();
        }
        $this->name = $name; $this->description = $description; $this->clientId = $clientId;
        $this->managerId = $managerId; $this->technologyIds = array_values($technologyIds);
        $this->startDate = $startDate; $this->endDate = $endDate;
        $this->recordEvent(ProjectWasUpdated::from($this));
    }

    public function updateDevelopment(DevelopmentStatus $status, ?DevelopmentNotes $notes, DevelopmentProgress $progress): void
    {
        $this->developmentStatus = $status;
        $this->developmentNotes = $notes;
        $this->developmentProgress = $progress;
        $this->recordEvent(new ProjectDevelopmentWasUpdated($this->id, $status, $progress, new DateTimeImmutable()));
    }

    // getters
}
```

### Domain Services

- `CreateProjectService`: valida que Client existe, User existe, todas las Technology IDs existen. Lanza `ClientNotFoundException`, `UserNotFoundException`, `TechnologyNotFoundException` si fallan.
- `UpdateProjectService`: idem.
- `UpdateProjectDevelopmentService`: muta el aggregate Project (no es aggregate propio).

### XML mapping específico

- `technologyIds` se mapea como **many-to-many** con la tabla pivote (`project_technology` o similar — mirar migración legacy).
- `clientId`, `managerId` con `<aggregate>_id` custom type del Plan 2/3.
- Campos de Development como columnas planas en la misma tabla `project` o tabla satélite si la migración legacy las separó. Si están separadas, mapear como **embedded** Doctrine o como join 1:1.

```xml
<entity name="App\Core\Domain\Model\Aggregate\Project" table="project">
    <id name="id" type="project_id"/>
    <field name="name" type="project_name" column="name"/>
    <field name="description" type="text" column="description" nullable="true"/>
    <field name="clientId" type="client_id" column="client_id"/>
    <field name="managerId" type="user_id" column="manager_id"/>
    <field name="startDate" type="datetime_immutable" column="start_date"/>
    <field name="endDate" type="datetime_immutable" column="end_date" nullable="true"/>
    <field name="developmentStatus" type="string" column="development_status" enum-type="App\Core\Domain\Model\VO\Project\DevelopmentStatus"/>
    <field name="developmentNotes" type="text" column="development_notes" nullable="true"/>
    <field name="developmentProgress" type="integer" column="development_progress"/>
    <field name="createdAt" type="datetime_immutable" column="created_at"/>
    <many-to-many field="technologyIds" target-entity="App\Core\Domain\Model\Aggregate\Technology">
        <join-table name="project_technology">
            <join-columns>
                <join-column name="project_id" referenced-column-name="id"/>
            </join-columns>
            <inverse-join-columns>
                <join-column name="technology_id" referenced-column-name="id"/>
            </inverse-join-columns>
        </join-table>
    </many-to-many>
</entity>
```

> Si la relación M2M está mal modelada para guardar IDs como VOs, podemos usar un VO custom-mapped o convertir la asociación a un array de string en BD. Decidir al implementar según lo que dicte el schema legacy.

### Borrado legacy

```bash
git rm -r src/ProjectManagement/Domain/Project src/ProjectManagement/Domain/Development \
          src/ProjectManagement/Infrastructure/Project src/ProjectManagement/Infrastructure/Development
git rm src/ProjectManagement/Application/{Create,Update,List}Project*/ \
       src/ProjectManagement/Application/UpdateDevelopment/
git rm src/ProjectManagement/Infrastructure/Http/{Create,Update,List}Project*Controller.php \
       src/ProjectManagement/Infrastructure/Http/UpdateDevelopmentController.php
```

---

## Slice 2 — Link

**Tabla**: `link`.
**Contexto legacy**: `src/ProjectManagement/.../Link*`.

### VOs

| VO | Reglas |
|---|---|
| `LinkId` | UUID |
| `LinkUrl` | URL válida (sub-tipo de `Url` común si existe) |
| `LinkLabel` | min 1, max 100, nullable |

### Referencias por VO

- `ProjectId`

### Eventos

- `LinkWasCreated(LinkId, ProjectId, LinkUrl)`
- `LinkWasDeleted(LinkId, ProjectId)`

### Acciones

| Acción | Tipo | Ruta |
|---|---|---|
| `CreateLink` | Command | `POST /projects/{projectId}/links` |
| `DeleteLink` | Command | `DELETE /links/{id}` |
| `ListLinksByProject` | Query | `GET /projects/{projectId}/links` |

### Aggregate

```php
class Link extends AggregateRoot
{
    private function __construct(
        private readonly LinkId $id,
        private readonly ProjectId $projectId,
        private LinkUrl $url,
        private ?LinkLabel $label,
        private readonly DateTimeImmutable $createdAt,
    ) {}

    public static function create(LinkId $id, ProjectId $projectId, LinkUrl $url, ?LinkLabel $label = null): self
    {
        $instance = new self($id, $projectId, $url, $label, new DateTimeImmutable());
        $instance->recordEvent(LinkWasCreated::from($instance));
        return $instance;
    }

    // getters
}
```

### Domain Services

- `CreateLinkService`: valida que `ProjectId` existe (consulta `ProjectRepository::find`).

### Borrado legacy

```bash
git rm src/ProjectManagement/Application/{CreateLink,DeleteLink}/ \
       src/ProjectManagement/Infrastructure/Http/{Create,Delete}LinkController.php
# (y entidad / repo Link si están separados)
```

---

## 🚧 Gate D — Verificación tras merge

```bash
git checkout feature/ddd-refactor
git merge --no-ff ddd/project
git merge --no-ff ddd/link
```

Conflicto esperado: ambos worktrees pueden haber creado `src/Core/Domain/Model/VO/Project/ProjectId.php` con código idéntico. Aceptar la versión del worktree de Project (o ambas, son iguales). Cualquier divergencia se resuelve manualmente.

- [ ] `make composer ARGS="phpstan deptrac"` verde
- [ ] `make bin-console ARGS="doctrine:schema:validate"` verde
- [ ] `make tests-unit` verde
- [ ] Smoke: CRUD Project, UpdateDevelopment, CRUD Link
- [ ] CI verde

```bash
git push origin feature/ddd-refactor
git tag -a plan-04-complete -m "Plan 4: Lote C (Project + Link) completados (Gate D)"
git push origin plan-04-complete
```

---

## Siguiente plan

Plan 5: **Lote D — Slices ProjectUser + TimeEntry**. Documento: `2026-05-14-05-lote-d-slices.md`.
