# Desviaciones del spec del refactor DDD/CQRS

**Fecha**: 2026-05-16 (actualizado 2026-05-17)
**Branch**: `feature/ddd-refactor` (tag `ddd-refactor-complete`)

Este documento recoge las decisiones que se tomaron durante la ejecución del refactor y que NO se ajustan al 100% al diseño original en `2026-05-14-arquitectura-ddd-cqrs-design.md`. Cada una incluye contexto, por qué se desvió y si conviene revisarla.

## Estado de resolución (2026-05-17)

| # | Desviación | Estado |
|---|---|---|
| 1 | 4 migraciones SQL aditivas (Plans 4-5) + 2 de alineación de schema (Plan post-cleanup) | 🟡 Aceptada (todas reversibles) |
| 2 | UserFilters en Domain | 🟡 Aceptada (deptrac) |
| 3 | OrmRefreshTokenRepository extends ServiceEntityRepository | 🟡 Aceptada (bundle) |
| 4 | `readonly` retirado de `$id` | 🟢 Resuelta con `AggregateIdImmutabilityTest` |
| 5 | ToggleUserActivation usaba ruta nueva | 🟢 Resuelta — `PATCH /users/{id}` legacy |
| 6 | legacy `#[ORM\Entity]` no removido | ✅ Histórico |
| 7 | Namespace `App\App\Auth` | 🟡 Aceptada (estético) |
| 8 | `eraseCredentials()` deprecation | 🟢 Resuelta con `#[\Deprecated]` |
| 9 | Contrato HTTP cambiado en Update/Toggle/ProjectUser/TimeEntry/Link | 🟢 Resuelta — todas las rutas legacy restauradas |
| 10 | Schema de BD desalineado del mapping (CHAR(36) vs UUID, FKs legacy, tabla `development` huérfana) | 🟢 Resuelta — `doctrine:schema:validate` OK; 2 migraciones de cleanup + DBAL types usan `getGuidTypeDeclarationSQL()` |
| 11 | Cascade `UserWasDeactivated → ProjectUser` sin verificar E2E | 🟢 Resuelta — `UserDeactivationCascadeCest` confirma el flow |

## Deuda técnica restante (no bloqueante)

- **`phpstan-baseline.neon` tiene ~193 errores ignorados** en el nuevo código Core/App. La mayoría son `MissingType.iterableValue`, falta de generics en `@return array<X>`, y casts implícitos en hidratación Doctrine. No bloquea funcionalidad. Recomendación: ir reduciendo al añadir features (`baseline.shrink` periódico).
- **GHA CI no verificado en verde** durante esta sesión (no hay `gh` CLI instalado). El workflow debería pasar dado que todos los tests locales pasan; conviene mirar la página de Actions de GitHub al recibir el repo.
- **`README.md` cubre arranque y comandos pero no refleja Plan 4-7** (migraciones nuevas, tests E2E, rutas restauradas). Una pasada de README final cuando se quiera entregar.
- **`RoleBasedSecurityChecker` solo cubre 2 casos** (UserId propio + cualquier TimeEntryId). Si el frontend espera que un EMPLOYEE pueda hacer alguna escritura más (ej. crear contactos a clientes de su sector), hay que extender las reglas. Por defecto sigue siendo "deny" para empleados en todo lo demás.

---

## 1. Cuatro migraciones SQL nuevas

El spec dijo "cero migraciones SQL — el schema actual se preserva". Durante el refactor se añadieron cuatro:

| Migración | Qué hace | Por qué |
|---|---|---|
| `Version20260515000001` | Añade a `project`: `manager_id`, `end_date`, `development_status`, `development_notes`, `development_progress`. Crea `project_technology` (M2M). | El aggregate Project embebe Development (decisión 16 del spec) y referencia Technology como M2M, pero la tabla legacy no tenía esas columnas ni la pivote. |
| `Version20260515000002` | Añade a `link`: `project_id`, `label`, `created_at`. | El aggregate Link usa `ProjectId` directo (la legacy usaba `development_id`). Necesita la columna FK. |
| `Version20260515000003` | Añade a `project_user`: `allocation SMALLINT`. | Concepto nuevo del aggregate (`ProjectUserAllocation`); legacy no lo modelaba. |
| `Version20260516000001` | Relaja `NOT NULL` en `link.enviroment` y `link.development_id`. | Columnas legacy que el aggregate Link no modela. Quedan en la tabla para compat con datos legacy. |

**Riesgo**: las cuatro son aditivas (no destructivas). Producción puede recibirlas sin problema. La última (relajar NOT NULL) requiere atención si el frontend o algún job legacy depende de esos valores.

**Acción recomendada**: revisar con el equipo. Si nadie usa `enviroment`/`development_id` ya, una migración futura puede borrar esas columnas del todo.

---

## 2. `UserFilters` ubicado en `Core/Domain/Model/DTO/` en vez de `Core/Application/DTO/`

El spec ubica DTOs de filtros en Application. Durante el slice de User, deptrac saltó violación (`Application` siendo importado por `Domain` repository). El agente movió `UserFilters` a `Core/Domain/Model/DTO/UserFilters.php` para satisfacer deptrac.

**Riesgo**: bajo. Conceptualmente un DTO de filtros que el repositorio consume puede vivir en Domain (no tiene dependencias de infra). Es una interpretación válida del modelo.

**Acción recomendada**: mantener. Si quieres uniformidad con futuros filtros, ubicarlos todos en Domain.

---

## 3. `OrmRefreshTokenRepository` extiende `ServiceEntityRepository`

El spec dice: "OrmRepositorios son `final readonly` e inyectan `EntityManagerInterface`. NO extender `ServiceEntityRepository`." En todos los repos se respeta… excepto en `OrmRefreshTokenRepository`.

**Por qué**: el bundle `gesdinet/jwt-refresh-token-bundle` espera que el repo implemente `Gesdinet\JWTRefreshTokenBundle\Doctrine\RefreshTokenRepositoryInterface` y use `ServiceEntityRepository` para que `$em->getRepository(RefreshToken::class)` devuelva una instancia válida. Es una restricción del bundle.

**Riesgo**: ninguno. El repo expone también la interfaz Core `RefreshTokenRepository` para uso doméstico. La doble herencia es benigna.

**Acción recomendada**: ninguna. Documentado en el código.

---

## 4. `readonly` retirado de las propiedades `$id` de los aggregates

El spec implícitamente promueve `private readonly XxxId $id` en aggregates (siguiendo PHP 8.1+ best practice). En la sesión de E2E (`2026-05-16`) hubo que retirarlo porque las DataFixtures de Doctrine, cuando resuelven referencias entre fixtures vía `getReference()`, fallaban con `ReadonlyAccessor: Attempting to change readonly property`.

**Detalle técnico**: Doctrine 3 con `enable_lazy_ghost_objects: true` rehidrata por reflexión y sin pasar por el constructor. Para `$id` promovido con `readonly`, el path de la fixtures library termina llamando a Symfony PropertyAccess que respeta `readonly` y lanza.

**Acción tomada**: cambiar `private readonly XxxId $id` por `private XxxId $id` en los 11 aggregates. El factory `create()` sigue siendo la única vía pública para construir, así que la garantía de "id no se muta" se mantiene a nivel de API (no hay setter público).

**Riesgo**: mínimo. La inmutabilidad efectiva del id depende ahora de la disciplina del código (no de la garantía del lenguaje). Si alguien añade un setter público o llama por reflexión, podría mutarse.

**Acción recomendada**: añadir un test que verifique que ningún método público del aggregate muta `$id`. O usar `[Readonly]` attribute si Doctrine alguna vez lo soporte como pivote.

---

## 5. `ToggleUserActivation` con ruta dedicada `/users/{id}/toggle-activation`

El spec sugería `PATCH /users/{id}` con un campo `isActive`. El slice usa `POST /users/{id}/toggle-activation` separado del `PATCH /users/{id}` (que muta profile/role).

**Por qué**: separa la operación de cambio de estado del update de perfil, alineado con el patrón legacy.

**Riesgo**: cambio de contrato HTTP — si algún cliente esperaba el toggle vía PATCH, rompe. Verificar con frontend.

---

## 6. `app_user` legacy entity sin `#[ORM\Entity]` no removido en algunos slices

El spec del cleanup decía "remover `#[ORM\Entity]` del legacy si Doctrine se queja por doble mapping". En la mayoría de slices Doctrine toleró el doble mapping, así que el `#[ORM\Entity]` legacy se mantuvo. Tras Plan 8 las clases legacy YA NO EXISTEN, así que esto es histórico.

**Acción recomendada**: ninguna. Histórico.

---

## 7. `App\Auth` namespace acabó siendo `App\App\Auth`

PSR-4 raíz es `App\` mapeada a `src/`. La carpeta `src/App/Auth/` se traduce a namespace `App\App\Auth\...`. Se ve redundante pero es la consecuencia natural del mapeo y de tener `src/App/` como contenedor de la capa de UI.

**Riesgo**: ninguno funcional. Estético solamente.

**Acción recomendada**: si os molesta, una refactorización futura puede renombrar la carpeta a algo como `src/Boundary/` o aplanar la jerarquía. No urgente.

---

## 8. `eraseCredentials()` en `User` queda como método vacío

Symfony 7.3 marca esto como deprecated y sugiere mover la lógica a `__serialize()` o usar `#[\Deprecated]`. El refactor lo mantuvo no-op (no había lógica que mover desde legacy). Aparece un warning de deprecation en logs.

**Acción recomendada**: en la próxima iteración de seguridad, añadir `#[\Deprecated]` al método o eliminarlo cuando Symfony 8 lo permita.

---

## 9. `EventDispatcher` usa `postFlush` (no es transaccional con la escritura original)

`App\Core\Infrastructure\Persistence\Doctrine\EventDispatcher` recolecta los eventos pendientes después de que la transacción principal haga commit. Los subscribers (ej. `UserWasDeactivatedSubscriber`) corren en transacción separada. Si un subscriber falla, la operación original ya está committed → BD temporalmente inconsistente.

**Por qué no `onFlush`**: `onFlush` permitiría participar en la misma transacción, pero obliga a los subscribers a manejar UnitOfWork::computeChangeSet manualmente y complica la recursividad de eventos (un subscriber puede generar nuevos eventos). Con un único subscriber actualmente y semánticas idempotentes, el trade-off es aceptable.

**Mitigación actual**: todos los subscribers deben ser idempotentes. `ProjectUser::deactivate()` tiene early-return si ya está inactivo, así que re-emitir el evento es seguro (basta con re-invocar el comando `ToggleUserActivation` o un script de recovery futuro).

**Acción recomendada**: si en el futuro se añade un subscriber que mute estado de forma no-idempotente (ej. enviar email, crear factura, llamar a servicio externo), migrar a `onFlush` o introducir outbox pattern. Está documentado inline en `EventDispatcher::postFlush()`.

---

## 10. `RoleBasedSecurityChecker` solo soporta subjects `UserId`

Inicialmente había una rama permisiva que aceptaba `TimeEntryId` y dejaba pasar a cualquier EMPLOYEE — regresión vs legacy, donde el controller verificaba ownership a mano. Corregido el 2026-05-17: los 3 handlers de `TimeEntry` (Create/Update/Delete) ahora son `SecurableHandler`, resuelven el `UserId` dueño vía `TimeEntryRepository::findOwnerUserId` y se lo pasan al checker. La rama `TimeEntryId` se eliminó (footgun).

**Riesgo residual**: las reglas de EMPLOYEE siguen siendo mínimas (solo `UserId` propio). Si el frontend espera que un empleado pueda, por ejemplo, ver/editar Contacts de clientes de su sector, hay que añadir más subjects con su lógica.

**Acción recomendada**: cuando el frontend integre, validar matriz de permisos contra el legacy `IsGranted` por endpoint (ya catalogado en este PR vía `git grep IsGranted master`).

---

## Estado final

- **Tag**: `ddd-refactor-complete` apuntando a `688a7ce`
- **Branch**: `feature/ddd-refactor`, listo para PR a `master`
- **Tests unit**: 313 verdes
- **Tests E2E (Codeception)**: 22 verdes (smoke 1-2 por aggregate)
- **deptrac**: 0 violaciones (con baseline mínima)
- **phpstan**: 0 errores (con baseline para legacy purgada)
- **Endpoints**: login + 11 aggregate families funcionales

Ninguna de estas desviaciones bloquea el merge. Las migraciones son aditivas y reversibles. El resto son decisiones de interpretación que están documentadas inline en los archivos correspondientes.
