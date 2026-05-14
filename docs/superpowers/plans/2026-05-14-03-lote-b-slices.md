# Plan 3 — Lote B — Slices verticales (Client, Contact)

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development. 2 agentes en paralelo en worktrees aislados. Cada uno aplica `docs/superpowers/plans/recipe-aggregate-slice.md` con sus parámetros.

**Goal:** Migrar Client (→Sector) y Contact (→Client) al modelo DDD/CQRS.

**Prerequisitos:** Plan 2 mergeado (`plan-02-complete`).

---

## Ejecución paralela

```bash
git worktree add ../wt-client  -b ddd/client  feature/ddd-refactor
git worktree add ../wt-contact -b ddd/contact feature/ddd-refactor
```

---

## Slice 1 — Client

**Tabla**: `client`.
**Contexto legacy**: `src/ClientManagement/Domain/Client/`, `src/ClientManagement/Infrastructure/Client/`, controllers en `src/ClientManagement/Infrastructure/Http/{Create,Update,Delete,Get,List,ToggleActivation}Client*`.

### VOs

| VO | Reglas |
|---|---|
| `ClientId` | UUID |
| `ClientName` | min 2, max 150, único en BD |
| `ClientCIF` (opcional) | Si la BD lo tiene; validar formato CIF/NIF español o regex permisivo |

> `Phone` y `Url` ya viven en `Core/Domain/Model/VO/Common/` si fueron creados en Plan 2 (User). Si no, este slice los crea.

### Referencias por VO

- `SectorId` (ya existe del Plan 2)

### Eventos

- `ClientWasCreated(ClientId, ClientName, SectorId)`
- `ClientWasUpdated(ClientId)`
- `ClientWasActivated(ClientId)` / `ClientWasDeactivated(ClientId)`

### Acciones

| Acción | Tipo | Ruta |
|---|---|---|
| `CreateClient` | Command | `POST /clients` |
| `UpdateClient` | Command | `PUT /clients/{id}` |
| `DeleteClient` | Command | `DELETE /clients/{id}` |
| `ToggleClientActivation` | Command | `POST /clients/{id}/toggle-activation` |
| `GetClient` | Query | `GET /clients/{id}` |
| `ListClients` | Query | `GET /clients` (paginado, filtros) |

### Aggregate

```php
class Client extends AggregateRoot
{
    private function __construct(
        private readonly ClientId $id,
        private ClientName $name,
        private SectorId $sectorId,
        private ?Phone $phone,
        private ?Url $website,
        private ?ClientCIF $cif,
        private bool $isActive,
        private readonly DateTimeImmutable $createdAt,
    ) {}

    public static function create(
        ClientId $id, ClientName $name, SectorId $sectorId,
        ?Phone $phone = null, ?Url $website = null, ?ClientCIF $cif = null,
    ): self {
        $instance = new self($id, $name, $sectorId, $phone, $website, $cif, true, new DateTimeImmutable());
        $instance->recordEvent(ClientWasCreated::from($instance));
        return $instance;
    }

    public function update(ClientName $name, SectorId $sectorId, ?Phone $phone, ?Url $website, ?ClientCIF $cif): void
    {
        $this->name = $name; $this->sectorId = $sectorId; $this->phone = $phone; $this->website = $website; $this->cif = $cif;
        $this->recordEvent(ClientWasUpdated::from($this));
    }

    public function activate(): void   { if (!$this->isActive) { $this->isActive = true; $this->recordEvent(ClientWasActivated::from($this)); } }
    public function deactivate(): void { if ($this->isActive)  { $this->isActive = false; $this->recordEvent(ClientWasDeactivated::from($this)); } }
    public function toggleActivation(): void { $this->isActive ? $this->deactivate() : $this->activate(); }

    // getters
}
```

### Domain Services

- `CreateClientService`: valida `ClientName` único, valida que el `SectorId` exista (consulta `SectorRepository`). Lanza `DuplicatedClientNameException` o `SectorNotFoundException`.
- `UpdateClientService`: idem para name si cambia.
- `DeleteClientService`: bloquea si hay Contacts asociados (consulta `ContactRepository`).

### XML mapping

Tabla `client`. Verificar columnas en `migrations/`.

### Borrado legacy

```bash
git rm -r src/ClientManagement/Domain/Client \
          src/ClientManagement/Infrastructure/Client
git rm src/ClientManagement/Application/{Create,Update,Delete,Get,List,ToggleClientActivation}Client*/
git rm src/ClientManagement/Infrastructure/Http/{Create,Update,Delete,Get,List,ToggleClientActivation}*Controller.php
```

---

## Slice 2 — Contact

**Tabla**: `contact`.
**Contexto legacy**: `src/ClientManagement/Domain/Contact/`, `src/ClientManagement/Infrastructure/Contact/`, controllers `*Contact*`.

### VOs

| VO | Reglas |
|---|---|
| `ContactId` | UUID |
| `ContactName` | min 2, max 100 |
| `ContactPosition` (opcional) | max 100 |

`Email`, `Phone` ya en `Common/` (de Plans previos).

### Referencias por VO

- `ClientId`

### Eventos

- `ContactWasCreated(ContactId, ClientId)`
- `ContactWasUpdated(ContactId)`
- `ContactWasMarkedAsMain(ContactId, ClientId)` — solo un contact es "main" por client; al marcar uno, los demás se desmarcan
- `ContactWasDeleted(ContactId)`

### Acciones

| Acción | Tipo | Ruta |
|---|---|---|
| `CreateContact` | Command | `POST /contacts` |
| `UpdateContact` | Command | `PUT /contacts/{id}` |
| `DeleteContact` | Command | `DELETE /contacts/{id}` |
| `MarkContactAsMain` | Command | `POST /contacts/{id}/mark-as-main` |
| `ListContacts` | Query | `GET /contacts?clientId=...` |

### Aggregate

```php
class Contact extends AggregateRoot
{
    private function __construct(
        private readonly ContactId $id,
        private readonly ClientId $clientId,
        private ContactName $name,
        private ?Email $email,
        private ?Phone $phone,
        private ?ContactPosition $position,
        private bool $isMain,
        private readonly DateTimeImmutable $createdAt,
    ) {}

    public static function create(
        ContactId $id, ClientId $clientId, ContactName $name,
        ?Email $email = null, ?Phone $phone = null, ?ContactPosition $position = null,
    ): self {
        $instance = new self($id, $clientId, $name, $email, $phone, $position, false, new DateTimeImmutable());
        $instance->recordEvent(ContactWasCreated::from($instance));
        return $instance;
    }

    public function markAsMain(): void
    {
        if ($this->isMain) return;
        $this->isMain = true;
        $this->recordEvent(ContactWasMarkedAsMain::from($this));
    }

    public function unmarkAsMain(): void { $this->isMain = false; /* no event — es transición silenciosa controlada por el service */ }

    public function update(ContactName $name, ?Email $email, ?Phone $phone, ?ContactPosition $position): void
    {
        $this->name = $name; $this->email = $email; $this->phone = $phone; $this->position = $position;
        $this->recordEvent(ContactWasUpdated::from($this));
    }
}
```

### Domain Services

- `MarkContactAsMainService`: usa `ContactRepository::findMainByClient(ClientId)` para localizar el actual main, le llama `unmarkAsMain()`, luego marca el nuevo. Persiste ambos.
- `DeleteContactService`: si el contact era main, no requiere acción especial pero deja al client sin main (regla aceptada — la UI elegirá uno nuevo).

### Borrado legacy

```bash
git rm -r src/ClientManagement/Domain/Contact src/ClientManagement/Infrastructure/Contact
git rm src/ClientManagement/Application/{Create,Update,Delete,MarkContactAsMain,List}Contact*/
git rm src/ClientManagement/Infrastructure/Http/{Create,Update,Delete,MarkContactAsMain,List}Contact*Controller.php
```

Tras el slice de Contact, `src/ClientManagement/` queda vacía (si Sector ya se borró en Plan 2). Si es así, eliminarla:

```bash
[ -d src/ClientManagement ] && rmdir src/ClientManagement
```

---

## 🚧 Gate C — Verificación tras merge

```bash
git checkout feature/ddd-refactor
git merge --no-ff ddd/client
git merge --no-ff ddd/contact
```

- [ ] `make composer ARGS="phpstan deptrac"` verde
- [ ] `make bin-console ARGS="doctrine:schema:validate"` verde
- [ ] `make tests-unit` verde
- [ ] Smoke: CRUD Client, CRUD Contact, MarkAsMain
- [ ] CI verde

```bash
git push origin feature/ddd-refactor
git tag -a plan-03-complete -m "Plan 3: Lote B (Client + Contact) completados (Gate C)"
git push origin plan-03-complete
```

---

## Siguiente plan

Plan 4: **Lote C — Slices Project + Link**. Documento: `2026-05-14-04-lote-c-slices.md`.
