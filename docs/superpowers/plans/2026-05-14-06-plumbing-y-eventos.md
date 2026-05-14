# Plan 6 — Plumbing transversal final + eventos cross-aggregate

> **For agentic workers:** REQUIRED SUB-SKILL: superpowers:executing-plans (no paralelo — son cambios transversales). Si los subscribers cross-aggregate son varios e independientes, esa sub-fase sí se puede paralelizar con subagent-driven-development.

**Goal:** Activar el `ExceptionListener`, sustituir el `PermissiveSecurityChecker` por una implementación basada en `SystemRole`, wirear el despacho de eventos `postFlush`, y crear los subscribers cross-aggregate.

**Prerequisitos:** Plan 5 mergeado (`plan-05-complete`).

---

## Estructura de cambios

```
src/App/UI/API/Response/Service/
  ExceptionListener.php                              # MODIFICADO (se suscribe a kernel.exception)
  MapperExceptionToJsonErrorResponse.php             # MODIFICADO (mapeos completos)

src/Core/Domain/Service/Security/
  PermissiveSecurityChecker.php                      # BORRADO
  RoleBasedSecurityChecker.php                       # NUEVO

src/Core/Infrastructure/Persistence/Doctrine/
  EventDispatcher.php                                # NUEVO (postFlush listener)

src/Core/Application/EventSubscriber/
  ProjectUser/UserWasDeactivatedSubscriber.php       # NUEVO
  (otros subscribers identificados)

config/services.yaml                                 # MODIFICADO (suscripción de listener)
config/packages/doctrine.yaml                        # MODIFICADO (event subscriber)

src/App/UI/API/Controller/<*>/<*>Controller.php      # MODIFICADO (quitar try/catch + IsGranted residuales)
```

---

## Task 1: Completar `MapperExceptionToJsonErrorResponse`

**Files:** Modify `src/App/UI/API/Response/Service/MapperExceptionToJsonErrorResponse.php`

- [ ] **Step 1: Reescribir con todos los mapeos**

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
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Throwable;

final readonly class MapperExceptionToJsonErrorResponse
{
    public function __invoke(Throwable $exception, bool $returnGenericUnexpectedError = false): ?JsonResponse
    {
        // 403
        if ($exception instanceof ForbiddenException) {
            return $this->build($exception->errorCode, $exception->getMessage(), Response::HTTP_FORBIDDEN);
        }

        // 400 de validación del payload (Symfony Validator)
        if ($exception instanceof ValidationFailedException || $this->isUnprocessablePayload($exception)) {
            return new JsonResponse(
                new JsonContentErrorResponse(ErrorCode::PAYLOAD_VALIDATION_FAILED->value, $exception->getMessage()),
                Response::HTTP_BAD_REQUEST,
            );
        }

        // Excepciones de dominio
        if ($exception instanceof CustomException) {
            $code = $exception->errorCode;
            $status = $this->statusFor($code);
            return $this->build($code, $exception->getMessage(), $status);
        }

        // 401 (Authentication)
        if ($exception instanceof HttpException && $exception->getStatusCode() === Response::HTTP_UNAUTHORIZED) {
            return new JsonResponse(
                new JsonContentErrorResponse(ErrorCode::FORBIDDEN->value, 'TR_UNAUTHENTICATED'),
                Response::HTTP_UNAUTHORIZED,
            );
        }

        if ($returnGenericUnexpectedError) {
            return new JsonResponse(
                new JsonContentErrorResponse(ErrorCode::UNEXPECTED_ERROR->value, ''),
                Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }

        return null;
    }

    private function statusFor(ErrorCode $code): int
    {
        return match (true) {
            str_starts_with($code->value, 'INVALID_')           => Response::HTTP_BAD_REQUEST,
            str_ends_with($code->value, '_NOT_FOUND')           => Response::HTTP_NOT_FOUND,
            str_starts_with($code->value, 'DUPLICATED_')        => Response::HTTP_CONFLICT,
            $code === ErrorCode::FORBIDDEN                      => Response::HTTP_FORBIDDEN,
            $code === ErrorCode::REFRESH_TOKEN_EXPIRED,
            $code === ErrorCode::REFRESH_TOKEN_REVOKED          => Response::HTTP_UNAUTHORIZED,
            default                                              => Response::HTTP_BAD_REQUEST,
        };
    }

    private function build(ErrorCode $code, string $message, int $status): JsonResponse
    {
        return new JsonResponse(new JsonContentErrorResponse($code->value, $message), $status);
    }

    private function isUnprocessablePayload(Throwable $e): bool
    {
        return $e instanceof HttpException && $e->getStatusCode() === Response::HTTP_UNPROCESSABLE_ENTITY;
    }
}
```

- [ ] **Step 2: Tests unitarios del mapper**

`tests/Unit/App/UI/API/Response/Service/MapperExceptionToJsonErrorResponseTest.php`:

```php
<?php

declare(strict_types=1);

namespace App\Tests\Unit\App\UI\API\Response\Service;

use App\App\UI\API\Response\Service\MapperExceptionToJsonErrorResponse;
use App\Core\Domain\Exception\Security\ForbiddenException;
use App\Core\Domain\Exception\VO\InvalidUserIdException;
use App\Core\Domain\Exception\User\UserNotFoundException;
use App\Core\Domain\Exception\User\DuplicatedUserEmailException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

final class MapperExceptionToJsonErrorResponseTest extends TestCase
{
    public function test_GivenForbiddenException_WhenMap_Then403(): void
    {
        $response = (new MapperExceptionToJsonErrorResponse())(new ForbiddenException());
        self::assertSame(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function test_GivenInvalidIdException_WhenMap_Then400(): void
    {
        $response = (new MapperExceptionToJsonErrorResponse())(new InvalidUserIdException('bad'));
        self::assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function test_GivenNotFoundException_WhenMap_Then404(): void
    {
        $response = (new MapperExceptionToJsonErrorResponse())(new UserNotFoundException('id'));
        self::assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function test_GivenDuplicatedException_WhenMap_Then409(): void
    {
        $response = (new MapperExceptionToJsonErrorResponse())(new DuplicatedUserEmailException('a@b.c'));
        self::assertSame(Response::HTTP_CONFLICT, $response->getStatusCode());
    }
}
```

- [ ] **Step 3: Run tests → PASS**

- [ ] **Step 4: Commit**

```bash
git add src/App/UI/API/Response/Service/MapperExceptionToJsonErrorResponse.php \
        tests/Unit/App/UI/API/Response/Service
git commit -m "feat(app): complete exception→HTTP mapper"
```

---

## Task 2: Suscribir `ExceptionListener`

**Files:** Modify `config/services.yaml`

- [ ] **Step 1: Añadir tag**

```yaml
    App\App\UI\API\Response\Service\ExceptionListener:
        tags:
            - { name: kernel.event_listener, event: kernel.exception }
```

- [ ] **Step 2: Verificar manualmente**

```bash
curl -X GET http://localhost/users/not-a-uuid -H "Authorization: Bearer $T"
# Expected: 400 con body {"code":"INVALID_USER_ID","message":"TR_INVALID_USER_ID"}
```

```bash
curl -X GET http://localhost/users/00000000-0000-4000-8000-000000000999 -H "Authorization: Bearer $T"
# Expected: 404 con body {"code":"USER_NOT_FOUND","message":"TR_USER_NOT_FOUND"}
```

- [ ] **Step 3: Commit**

```bash
git add config/services.yaml
git commit -m "feat(app): subscribe ExceptionListener to kernel.exception"
```

---

## Task 3: `RoleBasedSecurityChecker` (sustituye Permissive)

**Files:** Create `src/Core/Domain/Service/Security/RoleBasedSecurityChecker.php`, delete `PermissiveSecurityChecker.php`, modify `config/services.yaml`

- [ ] **Step 1: Implementar checker real**

```php
<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\Security;

use App\Core\Application\DTO\Security\SecurityToken;
use App\Core\Domain\Exception\Security\ForbiddenException;
use App\Shared\Domain\Enum\SystemRole;

final class RoleBasedSecurityChecker implements SecurityChecker
{
    public function grants(SecurityToken $securityToken, object $subject): void
    {
        // Reglas mínimas: ADMIN puede todo (ya filtrado por SecurityAwareTrait antes de llegar aquí).
        // EMPLOYEE solo puede operar sobre su propio UserId si el subject es UserId.
        // Otros casos: por defecto denegamos.

        $role = $securityToken->role;

        if ($role === SystemRole::EMPLOYEE) {
            // Si el subject es un UserId que coincide con el suyo, permitir.
            if ($subject instanceof \App\Core\Domain\Model\VO\User\UserId) {
                if ((string) $subject === $securityToken->authUserId) {
                    return;
                }
                throw new ForbiddenException('user-action');
            }

            // TimeEntry: un employee puede crear/editar/borrar sus propios time entries.
            if ($subject instanceof \App\Core\Domain\Model\VO\TimeEntry\TimeEntryId) {
                return; // El service valida ownership al cargar el aggregate.
            }

            // Cualquier otra cosa: denegar para employees.
            throw new ForbiddenException('role-' . $role->value);
        }

        // Otros roles futuros: denegar por defecto.
        throw new ForbiddenException('role-' . $role->value);
    }
}
```

> Las reglas exactas dependen de los `IsGranted` que tenía el código viejo. Revisar cada controller original y replicar.

- [ ] **Step 2: Test del checker**

`tests/Unit/Core/Domain/Service/Security/RoleBasedSecurityCheckerTest.php`:

```php
<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Service\Security;

use App\Core\Application\DTO\Security\SecurityToken;
use App\Core\Domain\Exception\Security\ForbiddenException;
use App\Core\Domain\Model\VO\User\UserId;
use App\Core\Domain\Service\Security\RoleBasedSecurityChecker;
use App\Shared\Domain\Enum\SystemRole;
use App\Tests\Unit\Core\Domain\Mother\User\UserIdMother;
use PHPUnit\Framework\TestCase;

final class RoleBasedSecurityCheckerTest extends TestCase
{
    public function test_GivenEmployeeOperatingOnOwnId_WhenGrants_ThenAllowed(): void
    {
        $userId = UserIdMother::create();
        $token = new SecurityToken((string) $userId, SystemRole::EMPLOYEE);

        (new RoleBasedSecurityChecker())->grants($token, $userId);
        $this->expectNotToPerformAssertions();
    }

    public function test_GivenEmployeeOperatingOnAnotherUserId_WhenGrants_ThenForbidden(): void
    {
        $token = new SecurityToken('me-id', SystemRole::EMPLOYEE);
        $other = new UserId('00000000-0000-4000-8000-000000000999');

        $this->expectException(ForbiddenException::class);
        (new RoleBasedSecurityChecker())->grants($token, $other);
    }
}
```

- [ ] **Step 3: Borrar `PermissiveSecurityChecker`**

```bash
git rm src/Core/Domain/Service/Security/PermissiveSecurityChecker.php
```

- [ ] **Step 4: Wire en `config/services.yaml`**

Sustituir:

```yaml
    App\Core\Domain\Service\Security\SecurityChecker:
        class: App\Core\Domain\Service\Security\RoleBasedSecurityChecker
```

- [ ] **Step 5: Run tests + verificar API**

```bash
make tests-unit
# Smoke: como employee, intentar borrar otro user → debería devolver 403
```

- [ ] **Step 6: Commit**

```bash
git add src/Core/Domain/Service/Security tests/Unit/Core/Domain/Service/Security config/services.yaml
git commit -m "feat(security): replace permissive checker with role-based"
```

---

## Task 4: Limpiar `IsGranted` residuales

**Files:** Modify `src/App/UI/API/Controller/**/*.php`

- [ ] **Step 1: Buscar y quitar**

```bash
grep -rl "IsGranted" src/App/UI/API/Controller/
```

Por cada archivo encontrado, eliminar:
- `use Symfony\Component\Security\Http\Attribute\IsGranted;`
- `#[IsGranted(...)]` sobre la clase o el método

La autorización se hace en el handler vía `SecurableHandler::checkSecurity()`.

- [ ] **Step 2: Verificar suite**

```bash
make tests-unit
make tests-functional
```

- [ ] **Step 3: Commit**

```bash
git add src/App/UI/API
git commit -m "refactor(controllers): drop IsGranted attributes (auth moved to handlers)"
```

---

## Task 5: Listener Doctrine `postFlush` que despacha eventos

**Files:** Create `src/Core/Infrastructure/Persistence/Doctrine/EventDispatcher.php`, modify `config/services.yaml`

- [ ] **Step 1: Crear listener**

```php
<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Persistence\Doctrine;

use App\Core\Domain\Model\AggregateRoot;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Symfony\Component\Messenger\MessageBusInterface;

final class EventDispatcher
{
    public function __construct(private readonly MessageBusInterface $eventBus) {}

    public function postFlush(PostFlushEventArgs $args): void
    {
        $em = $args->getObjectManager();
        $uow = $em->getUnitOfWork();

        $entities = array_merge(
            $uow->getIdentityMap(),
            // Note: identity map covers inserted and managed entities
        );

        foreach ($uow->getIdentityMap() as $entitiesOfClass) {
            foreach ($entitiesOfClass as $entity) {
                if (!$entity instanceof AggregateRoot) {
                    continue;
                }
                foreach ($entity->pullEvents() as $event) {
                    $this->eventBus->dispatch($event);
                }
            }
        }
    }
}
```

- [ ] **Step 2: Registrar como Doctrine event listener**

En `config/services.yaml`:

```yaml
    App\Core\Infrastructure\Persistence\Doctrine\EventDispatcher:
        arguments: ['@messenger.bus.event.bus']
        tags:
            - { name: doctrine.event_listener, event: postFlush }
```

- [ ] **Step 3: Test funcional**

`tests/Functional/Core/Infrastructure/Persistence/Doctrine/EventDispatcherTest.php`:

```php
<?php

declare(strict_types=1);

namespace App\Tests\Functional\Core\Infrastructure\Persistence\Doctrine;

use App\Core\Domain\Model\Aggregate\Sector;
use App\Core\Domain\Model\Event\Sector\SectorWasCreated;
use App\Core\Domain\Model\Repository\SectorRepository;
use App\Tests\Unit\Core\Domain\Mother\Sector\SectorMother;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\MessageBusInterface;

final class EventDispatcherTest extends KernelTestCase
{
    public function test_GivenAggregateWithEvent_WhenFlush_ThenEventIsDispatched(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        /** @var EntityManagerInterface $em */
        $em = $container->get(EntityManagerInterface::class);
        /** @var SectorRepository $repo */
        $repo = $container->get(SectorRepository::class);

        // Substituir el event bus por uno que captura
        $captured = [];
        $bus = new class($captured) implements MessageBusInterface {
            public function __construct(private array &$captured) {}
            public function dispatch(object $message, array $stamps = []): \Symfony\Component\Messenger\Envelope {
                $this->captured[] = $message;
                return new \Symfony\Component\Messenger\Envelope($message);
            }
        };
        // ... wire bus en el container vía services_test.yaml (omitido por brevedad)

        $sector = SectorMother::create();
        $repo->add($sector);
        $em->flush();

        self::assertInstanceOf(SectorWasCreated::class, $captured[0] ?? null);
    }
}
```

- [ ] **Step 4: Commit**

```bash
git add src/Core/Infrastructure/Persistence/Doctrine/EventDispatcher.php \
        config/services.yaml tests/Functional/Core/Infrastructure
git commit -m "feat(core): wire postFlush domain event dispatcher"
```

---

## Task 6: Subscriber `UserWasDeactivated → ProjectUser`

**Files:** Create `src/Core/Application/EventSubscriber/ProjectUser/UserWasDeactivatedSubscriber.php`

> Este subscriber sustituye al método legacy `DoctrineUserRepository::updateUserActivationWithRelation` que violaba el bounded context.

- [ ] **Step 1: Crear subscriber**

```php
<?php

declare(strict_types=1);

namespace App\Core\Application\EventSubscriber\ProjectUser;

use App\Core\Domain\Model\Event\User\UserWasDeactivated;
use App\Core\Domain\Model\Repository\ProjectUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'event.bus')]
final readonly class UserWasDeactivatedSubscriber
{
    public function __construct(
        private ProjectUserRepository $projectUserRepository,
        private EntityManagerInterface $em,
    ) {}

    public function __invoke(UserWasDeactivated $event): void
    {
        $projectUsers = $this->projectUserRepository->findActiveByUser($event->userId);
        foreach ($projectUsers as $pu) {
            $pu->deactivate();
        }
        $this->em->flush();
    }
}
```

> Requisito: `ProjectUserRepository::findActiveByUser(UserId)` debe existir. Si no se añadió en Plan 5, añadirlo aquí.

- [ ] **Step 2: Test del subscriber**

`tests/Unit/Core/Application/EventSubscriber/ProjectUser/UserWasDeactivatedSubscriberTest.php`:

```php
<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Application\EventSubscriber\ProjectUser;

use App\Core\Application\EventSubscriber\ProjectUser\UserWasDeactivatedSubscriber;
use App\Core\Domain\Model\Aggregate\ProjectUser;
use App\Core\Domain\Model\Event\User\UserWasDeactivated;
use App\Core\Domain\Model\Repository\ProjectUserRepository;
use App\Tests\Unit\Core\Domain\Mother\User\UserIdMother;
use App\Tests\Unit\Core\Domain\Mother\ProjectUser\ProjectUserMother;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

final class UserWasDeactivatedSubscriberTest extends TestCase
{
    public function test_GivenEvent_WhenInvoke_ThenAllActiveProjectUsersAreDeactivated(): void
    {
        $userId = UserIdMother::create();
        $pu1 = ProjectUserMother::create(userId: $userId);
        $pu2 = ProjectUserMother::create(userId: $userId);

        $repo = $this->createMock(ProjectUserRepository::class);
        $repo->method('findActiveByUser')->willReturn([$pu1, $pu2]);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::once())->method('flush');

        (new UserWasDeactivatedSubscriber($repo, $em))(
            new UserWasDeactivated($userId, new DateTimeImmutable()),
        );

        self::assertFalse($pu1->isActive());
        self::assertFalse($pu2->isActive());
    }
}
```

- [ ] **Step 3: Commit**

```bash
git add src/Core/Application/EventSubscriber tests/Unit/Core/Application/EventSubscriber
git commit -m "feat(events): subscribe ProjectUser deactivation to UserWasDeactivated"
```

---

## Task 7: Otros subscribers identificados durante Plans 2-5

Repetir el patrón del Task 6 para otros subscribers cross-aggregate. Candidatos típicos:

- **`SectorDeletion` cuando hay Clients**: técnicamente no es subscriber sino validación pre-evento, vive en el Domain Service `DeleteSectorService`. Verificar que ya está.
- **`ProjectWasDeleted → Link cascada`**: si la regla es "borrar links del proyecto al borrar el proyecto", crear subscriber. Si BD lo hace por FK CASCADE, omitir.
- **`ProjectWasDeleted → TimeEntry`**: idem.

> Esta tarea es de descubrimiento. Revisar el código legacy antes de borrarlo definitivamente para identificar todas las reglas cross-aggregate y mover a subscribers o validations.

- [ ] **Step 1: Auditar** las relaciones cross-aggregate del código viejo
- [ ] **Step 2: Crear subscribers necesarios** siguiendo el patrón del Task 6
- [ ] **Step 3: Commit por subscriber**

---

## 🚧 Gate F — Verificación de Plan 6

- [ ] `make composer ARGS="phpstan deptrac"` verde
- [ ] `make tests-unit` + `make tests-functional` verdes
- [ ] Smoke: peticiones erróneas devuelven JSON `{code,message}` con HTTP correcto
- [ ] Smoke: desactivar un User propaga a ProjectUsers en BD
- [ ] CI verde

```bash
git push origin feature/ddd-refactor
git tag -a plan-06-complete -m "Plan 6: plumbing transversal + eventos cross-aggregate (Gate F)"
git push origin plan-06-complete
```

---

## Siguiente plan

Plan 7: **Tests E2E Codeception para los 11 aggregates**. Documento: `2026-05-14-07-tests-e2e.md`.
