# Receta canónica — Vertical slice por aggregate

> **No es un plan ejecutable**. Es el patrón al que se ajustan los Plans 2-5. Cada plan referencia esta receta y le pasa los valores específicos de su aggregate.

**Inputs por aggregate:**
- `<Aggregate>` — PascalCase singular (ej. `User`, `Client`)
- `<aggregate>` — lowercase singular (ej. `user`, `client`)
- `<aggregates>` — lowercase plural para rutas REST (ej. `users`)
- `<TableName>` — nombre de la tabla en BD (ej. `app_user`, `client`) — se obtiene mirando la entidad legacy
- Lista de **VOs** con sus reglas
- Lista de **Eventos** `<Aggregate>Was*`
- Lista de **Acciones** (Commands y Queries) con sus campos

**Resultado esperado tras aplicar la receta:**
- Aggregate rico en `Core/Domain/Model/Aggregate/<Aggregate>.php`
- VOs en `Core/Domain/Model/VO/<Aggregate>/`
- DBAL Types en `Core/Infrastructure/Persistence/Doctrine/DBAL/Types/`
- Excepciones de dominio en `Core/Domain/Exception/<Aggregate>/`
- Eventos en `Core/Domain/Model/Event/<Aggregate>/`
- Repository interface en `Core/Domain/Model/Repository/<Aggregate>Repository.php` + `Orm<Aggregate>Repository`
- XML mapping en `Core/Infrastructure/Persistence/Doctrine/ORM/Mapping/XML/<Aggregate>.orm.xml`
- Domain Services en `Core/Domain/Service/<Aggregate>/<Action>/`
- Commands/Queries + Handlers en `Core/Application/{Command,Query}/<Aggregate>/<Action>/`
- Controllers + Request/Response en `App/UI/API/Controller/<Aggregate>/<Action>/`
- Mothers en `tests/Unit/Core/Domain/Mother/<Aggregate>/`
- Tests unitarios (VO + Aggregate + Service + Handler)
- Borrado el código legacy del aggregate (su carpeta en `<Context>/`)

---

## Paso 1 — Crear los VOs

Para cada VO (`<Aggregate>Id`, `<Aggregate>Name`, etc.):

### 1.1 Test del VO

`tests/Unit/Core/Domain/Model/VO/<Aggregate>/<Aggregate>IdTest.php`:

```php
<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Model\VO\<Aggregate>;

use App\Core\Domain\Exception\VO\Invalid<Aggregate>IdException;
use App\Core\Domain\Model\VO\<Aggregate>\<Aggregate>Id;
use PHPUnit\Framework\TestCase;

final class <Aggregate>IdTest extends TestCase
{
    public function test_GivenValidUuid_WhenConstruct_ThenInstanceIsCreated(): void
    {
        $id = new <Aggregate>Id('00000000-0000-4000-8000-000000000000');
        self::assertSame('00000000-0000-4000-8000-000000000000', (string) $id);
    }

    public function test_GivenInvalidUuid_WhenConstruct_ThenThrowsInvalid<Aggregate>IdException(): void
    {
        $this->expectException(Invalid<Aggregate>IdException::class);
        new <Aggregate>Id('not-a-uuid');
    }

    public function test_GivenNull_WhenGenerate_ThenInstanceIsCreated(): void
    {
        $id = <Aggregate>Id::generate();
        self::assertNotEmpty((string) $id);
    }
}
```

### 1.2 Run test → FAIL

```bash
make tests-unit
```

### 1.3 Implementación

`src/Core/Domain/Model/VO/<Aggregate>/<Aggregate>Id.php`:

```php
<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\VO\<Aggregate>;

use App\Core\Domain\Exception\VO\Invalid<Aggregate>IdException;
use Symfony\Component\Uid\Uuid;

final readonly class <Aggregate>Id
{
    private string $value;

    public function __construct(string $value)
    {
        if (!Uuid::isValid($value)) {
            throw new Invalid<Aggregate>IdException($value);
        }
        $this->value = $value;
    }

    public static function generate(): self
    {
        return new self(Uuid::v4()->toRfc4122());
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
```

### 1.4 Excepción asociada

`src/Core/Domain/Exception/VO/Invalid<Aggregate>IdException.php`:

```php
<?php

declare(strict_types=1);

namespace App\Core\Domain\Exception\VO;

use App\Shared\Domain\Model\CustomException;
use App\Shared\Domain\Model\ErrorCode;

final class Invalid<Aggregate>IdException extends CustomException
{
    public function __construct(string $value)
    {
        parent::__construct(
            message: 'TR_INVALID_<AGGREGATE>_ID',
            errorCode: ErrorCode::INVALID_<AGGREGATE>_ID,
            messageParams: ['%value%' => $value],
        );
    }
}
```

### 1.5 Run test → PASS

### 1.6 Commit

```bash
git add src/Core/Domain/Model/VO/<Aggregate>/<Aggregate>Id.php \
        src/Core/Domain/Exception/VO/Invalid<Aggregate>IdException.php \
        tests/Unit/Core/Domain/Model/VO/<Aggregate>/<Aggregate>IdTest.php
git commit -m "feat(<aggregate>): add <Aggregate>Id VO"
```

### 1.7 Repetir 1.1–1.6 para cada VO adicional

Para VOs con reglas de longitud:

```php
final readonly class <Aggregate>Name
{
    private string $value;

    public function __construct(string $value)
    {
        $value = trim($value);
        if ($value === '' || mb_strlen($value) < 2 || mb_strlen($value) > 100) {
            throw new Invalid<Aggregate>NameException($value);
        }
        $this->value = $value;
    }

    public function __toString(): string { return $this->value; }
    public function equals(self $other): bool { return $this->value === $other->value; }
}
```

Para VOs comunes (`Email`, `Password`, `Phone`, `Url`) que ya existan de un slice anterior, **no se duplican**: vivirán en `Core/Domain/Model/VO/Common/` desde el primer slice que los necesite. El primer slice en necesitarlos los crea allí.

---

## Paso 2 — DBAL Types

Para cada VO con tipo no nativo (todos los `Id` y `Name`):

### 2.1 Crear el type

`src/Core/Infrastructure/Persistence/Doctrine/DBAL/Types/<Aggregate>IdType.php`:

```php
<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Persistence\Doctrine\DBAL\Types;

use App\Core\Domain\Model\VO\<Aggregate>\<Aggregate>Id;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

final class <Aggregate>IdType extends Type
{
    public const NAME = '<aggregate>_id';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL(['length' => 36, 'fixed' => true]);
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?<Aggregate>Id
    {
        return $value === null ? null : new <Aggregate>Id((string) $value);
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        return $value === null ? null : (string) $value;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
```

### 2.2 Registrar en `config/packages/doctrine.yaml`

Bajo `doctrine.dbal.types`:

```yaml
doctrine:
    dbal:
        types:
            <aggregate>_id: App\Core\Infrastructure\Persistence\Doctrine\DBAL\Types\<Aggregate>IdType
            <aggregate>_name: App\Core\Infrastructure\Persistence\Doctrine\DBAL\Types\<Aggregate>NameType
```

### 2.3 Commit

```bash
git add src/Core/Infrastructure/Persistence/Doctrine/DBAL/Types config/packages/doctrine.yaml
git commit -m "feat(<aggregate>): register DBAL types"
```

---

## Paso 3 — Excepciones de dominio del aggregate

Para cada excepción (`<Aggregate>NotFoundException`, `Duplicated<Aggregate>NameException`, etc.):

`src/Core/Domain/Exception/<Aggregate>/<Aggregate>NotFoundException.php`:

```php
<?php

declare(strict_types=1);

namespace App\Core\Domain\Exception\<Aggregate>;

use App\Shared\Domain\Model\CustomException;
use App\Shared\Domain\Model\ErrorCode;

final class <Aggregate>NotFoundException extends CustomException
{
    public function __construct(string $id = '')
    {
        parent::__construct(
            message: 'TR_<AGGREGATE>_NOT_FOUND',
            errorCode: ErrorCode::<AGGREGATE>_NOT_FOUND,
            messageParams: $id !== '' ? ['%id%' => $id] : [],
        );
    }
}
```

```bash
git add src/Core/Domain/Exception/<Aggregate>
git commit -m "feat(<aggregate>): add domain exceptions"
```

---

## Paso 4 — Aggregate rico

### 4.1 Test de factory + transición

`tests/Unit/Core/Domain/Model/Aggregate/<Aggregate>Test.php`:

```php
<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Model\Aggregate;

use App\Core\Domain\Model\Aggregate\<Aggregate>;
use App\Core\Domain\Model\Event\<Aggregate>\<Aggregate>WasCreated;
use App\Tests\Unit\Core\Domain\Mother\<Aggregate>\<Aggregate>Mother;
use PHPUnit\Framework\TestCase;

final class <Aggregate>Test extends TestCase
{
    public function test_GivenValidVOs_WhenCreate_ThenInstanceWithCreatedEvent(): void
    {
        $aggregate = <Aggregate>Mother::create();
        $events = $aggregate->pullEvents();
        self::assertCount(1, $events);
        self::assertInstanceOf(<Aggregate>WasCreated::class, $events[0]);
    }
}
```

### 4.2 Implementación

`src/Core/Domain/Model/Aggregate/<Aggregate>.php`:

```php
<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Aggregate;

use App\Core\Domain\Model\AggregateRoot;
use App\Core\Domain\Model\Event\<Aggregate>\<Aggregate>WasCreated;
use App\Core\Domain\Model\VO\<Aggregate>\{<Aggregate>Id, <Aggregate>Name};
use DateTimeImmutable;

class <Aggregate> extends AggregateRoot
{
    private function __construct(
        private readonly <Aggregate>Id $id,
        private <Aggregate>Name $name,
        private readonly DateTimeImmutable $createdAt,
        // ... más campos según el aggregate
    ) {}

    public static function create(
        <Aggregate>Id $id,
        <Aggregate>Name $name,
        // ... más VOs
    ): self {
        $instance = new self(
            id: $id,
            name: $name,
            createdAt: new DateTimeImmutable(),
        );

        $instance->recordEvent(<Aggregate>WasCreated::from($instance));

        return $instance;
    }

    public function id(): <Aggregate>Id { return $this->id; }
    public function name(): <Aggregate>Name { return $this->name; }
    public function createdAt(): DateTimeImmutable { return $this->createdAt; }

    public function rename(<Aggregate>Name $newName): void
    {
        if ($this->name->equals($newName)) {
            return;
        }
        $this->name = $newName;
        $this->recordEvent(<Aggregate>WasUpdated::from($this));
    }
}
```

> **El constructor es privado**. Doctrine lo invocará por reflexión al hidratar desde BD (esto funciona con XML mapping).

### 4.3 Commit

```bash
git add src/Core/Domain/Model/Aggregate/<Aggregate>.php tests/Unit/Core/Domain/Model/Aggregate
git commit -m "feat(<aggregate>): add rich aggregate with factory + create event"
```

---

## Paso 5 — Eventos de dominio

Para cada evento (`<Aggregate>WasCreated`, `<Aggregate>WasUpdated`, etc.):

`src/Core/Domain/Model/Event/<Aggregate>/<Aggregate>WasCreated.php`:

```php
<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Event\<Aggregate>;

use App\Core\Domain\Model\Aggregate\<Aggregate>;
use App\Core\Domain\Model\VO\<Aggregate>\<Aggregate>Id;
use DateTimeImmutable;

final readonly class <Aggregate>WasCreated
{
    public function __construct(
        public <Aggregate>Id $id,
        public DateTimeImmutable $occurredAt,
    ) {}

    public static function from(<Aggregate> $aggregate): self
    {
        return new self(
            id: $aggregate->id(),
            occurredAt: new DateTimeImmutable(),
        );
    }
}
```

```bash
git add src/Core/Domain/Model/Event/<Aggregate>
git commit -m "feat(<aggregate>): add domain events"
```

---

## Paso 6 — Repository interface

`src/Core/Domain/Model/Repository/<Aggregate>Repository.php`:

```php
<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Repository;

use App\Core\Domain\Exception\<Aggregate>\<Aggregate>NotFoundException;
use App\Core\Domain\Model\Aggregate\<Aggregate>;
use App\Core\Domain\Model\VO\<Aggregate>\<Aggregate>Id;

interface <Aggregate>Repository
{
    public function add(<Aggregate> $aggregate): void;
    public function remove(<Aggregate> $aggregate): void;
    public function find(<Aggregate>Id $id): ?<Aggregate>;

    /** @throws <Aggregate>NotFoundException */
    public function findOneOrFail(<Aggregate>Id $id): <Aggregate>;

    /** @return list<<Aggregate>> */
    public function all(): array;
}
```

> Métodos adicionales (`findByXxx`, `findOneByName`) se añaden según necesite el aggregate.

```bash
git add src/Core/Domain/Model/Repository/<Aggregate>Repository.php
git commit -m "feat(<aggregate>): add repository interface"
```

---

## Paso 7 — Orm Repository

`src/Core/Infrastructure/Persistence/Doctrine/ORM/Orm<Aggregate>Repository.php`:

```php
<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Persistence\Doctrine\ORM;

use App\Core\Domain\Exception\<Aggregate>\<Aggregate>NotFoundException;
use App\Core\Domain\Model\Aggregate\<Aggregate>;
use App\Core\Domain\Model\Repository\<Aggregate>Repository;
use App\Core\Domain\Model\VO\<Aggregate>\<Aggregate>Id;
use Doctrine\ORM\EntityManagerInterface;

final readonly class Orm<Aggregate>Repository implements <Aggregate>Repository
{
    public function __construct(private EntityManagerInterface $em) {}

    public function add(<Aggregate> $aggregate): void
    {
        $this->em->persist($aggregate);
    }

    public function remove(<Aggregate> $aggregate): void
    {
        $this->em->remove($aggregate);
    }

    public function find(<Aggregate>Id $id): ?<Aggregate>
    {
        return $this->em->find(<Aggregate>::class, $id);
    }

    public function findOneOrFail(<Aggregate>Id $id): <Aggregate>
    {
        return $this->find($id) ?? throw new <Aggregate>NotFoundException((string) $id);
    }

    public function all(): array
    {
        return $this->em->getRepository(<Aggregate>::class)->findAll();
    }
}
```

Bind en `config/services.yaml`:

```yaml
    App\Core\Domain\Model\Repository\<Aggregate>Repository:
        class: App\Core\Infrastructure\Persistence\Doctrine\ORM\Orm<Aggregate>Repository
```

```bash
git add src/Core/Infrastructure/Persistence/Doctrine/ORM/Orm<Aggregate>Repository.php config/services.yaml
git commit -m "feat(<aggregate>): add Doctrine ORM repository"
```

---

## Paso 8 — XML mapping

`src/Core/Infrastructure/Persistence/Doctrine/ORM/Mapping/XML/<Aggregate>.orm.xml`:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                  xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                                      https://www.doctrine-project.org/schemas/orm/doctrine-mapping.xsd">

    <entity name="App\Core\Domain\Model\Aggregate\<Aggregate>" table="<table_name>">

        <id name="id" type="<aggregate>_id" column="id">
            <generator strategy="NONE"/>
        </id>

        <field name="name" type="<aggregate>_name" column="name" length="100"/>
        <field name="createdAt" type="datetime_immutable" column="created_at"/>

        <!-- Más fields según el aggregate -->

    </entity>

</doctrine-mapping>
```

**Crucial**: el nombre de tabla (`<table_name>`) y los nombres de columna deben coincidir con la BD actual. Mirar la entidad legacy del aggregate (en `<Context>/Domain/<Aggregate>.php` o las migraciones de `migrations/`).

Verificar con:

```bash
make bin-console ARGS="doctrine:schema:validate"
```

Expected: "[OK] The mapping files are correct" + "[OK] The database schema is in sync with the mapping files".

```bash
git add src/Core/Infrastructure/Persistence/Doctrine/ORM/Mapping/XML/<Aggregate>.orm.xml
git commit -m "feat(<aggregate>): add XML mapping"
```

---

## Paso 9 — Domain Services

Por cada **acción de escritura** (`Create`, `Update`, `Delete`, `ToggleX`, etc.):

### 9.1 Test del servicio

`tests/Unit/Core/Domain/Service/<Aggregate>/Create<Aggregate>ServiceTest.php`:

```php
<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Service\<Aggregate>;

use App\Core\Domain\Exception\<Aggregate>\Duplicated<Aggregate>NameException;
use App\Core\Domain\Model\Repository\<Aggregate>Repository;
use App\Core\Domain\Service\<Aggregate>\Create<Aggregate>\Create<Aggregate>Service;
use App\Tests\Unit\Core\Domain\Mother\<Aggregate>\<Aggregate>IdMother;
use App\Tests\Unit\Core\Domain\Mother\<Aggregate>\<Aggregate>NameMother;
use PHPUnit\Framework\TestCase;

final class Create<Aggregate>ServiceTest extends TestCase
{
    public function test_GivenUniqueName_WhenInvoke_ThenAggregateIsAdded(): void
    {
        $repo = $this->createMock(<Aggregate>Repository::class);
        $repo->method('findOneByName')->willReturn(null);
        $repo->expects(self::once())->method('add');

        $service = new Create<Aggregate>Service($repo);
        $service(<Aggregate>IdMother::create(), <Aggregate>NameMother::create());
    }

    public function test_GivenDuplicatedName_WhenInvoke_ThenThrows(): void
    {
        $existing = $this->createMock(\App\Core\Domain\Model\Aggregate\<Aggregate>::class);
        $repo = $this->createMock(<Aggregate>Repository::class);
        $repo->method('findOneByName')->willReturn($existing);
        $repo->expects(self::never())->method('add');

        $this->expectException(Duplicated<Aggregate>NameException::class);

        (new Create<Aggregate>Service($repo))(
            <Aggregate>IdMother::create(),
            <Aggregate>NameMother::create(),
        );
    }
}
```

### 9.2 Interfaz

`src/Core/Domain/Service/<Aggregate>/Create<Aggregate>/Create<Aggregate>ServiceInterface.php`:

```php
<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\<Aggregate>\Create<Aggregate>;

use App\Core\Domain\Model\Aggregate\<Aggregate>;
use App\Core\Domain\Model\VO\<Aggregate>\<Aggregate>Id;
use App\Core\Domain\Model\VO\<Aggregate>\<Aggregate>Name;

interface Create<Aggregate>ServiceInterface
{
    public function __invoke(<Aggregate>Id $id, <Aggregate>Name $name): <Aggregate>;
}
```

### 9.3 Implementación

`src/Core/Domain/Service/<Aggregate>/Create<Aggregate>/Create<Aggregate>Service.php`:

```php
<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\<Aggregate>\Create<Aggregate>;

use App\Core\Domain\Exception\<Aggregate>\Duplicated<Aggregate>NameException;
use App\Core\Domain\Model\Aggregate\<Aggregate>;
use App\Core\Domain\Model\Repository\<Aggregate>Repository;
use App\Core\Domain\Model\VO\<Aggregate>\<Aggregate>Id;
use App\Core\Domain\Model\VO\<Aggregate>\<Aggregate>Name;

final readonly class Create<Aggregate>Service implements Create<Aggregate>ServiceInterface
{
    public function __construct(private <Aggregate>Repository $repository) {}

    public function __invoke(<Aggregate>Id $id, <Aggregate>Name $name): <Aggregate>
    {
        if ($this->repository->findOneByName($name) !== null) {
            throw new Duplicated<Aggregate>NameException((string) $name);
        }

        $aggregate = <Aggregate>::create(id: $id, name: $name);
        $this->repository->add($aggregate);
        return $aggregate;
    }
}
```

> El método `findOneByName` debe añadirse al `<Aggregate>Repository` interface en Paso 6 si la regla "nombre único" aplica.

```bash
git add src/Core/Domain/Service/<Aggregate>/Create<Aggregate> tests/Unit/Core/Domain/Service
git commit -m "feat(<aggregate>): add Create<Aggregate>Service"
```

### 9.4 Repetir 9.1-9.3 para Update, Delete, etc.

---

## Paso 10 — Commands + Handlers

Por cada **acción de escritura**:

### 10.1 Command

`src/Core/Application/Command/<Aggregate>/Create<Aggregate>/Create<Aggregate>Command.php`:

```php
<?php

declare(strict_types=1);

namespace App\Core\Application\Command\<Aggregate>\Create<Aggregate>;

use App\Core\Application\Bus\Command;
use App\Core\Application\DTO\Security\SecurityToken;

final readonly class Create<Aggregate>Command implements Command
{
    public function __construct(
        public SecurityToken $securityToken,
        public string $id,
        public string $name,
        // ... más campos primitivos según el aggregate
    ) {}
}
```

### 10.2 Test del handler

`tests/Unit/Core/Application/Command/<Aggregate>/Create<Aggregate>HandlerTest.php`:

```php
<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Application\Command\<Aggregate>;

use App\Core\Application\Command\<Aggregate>\Create<Aggregate>\Create<Aggregate>Command;
use App\Core\Application\Command\<Aggregate>\Create<Aggregate>\Create<Aggregate>Handler;
use App\Core\Application\DTO\Security\SecurityToken;
use App\Core\Domain\Service\<Aggregate>\Create<Aggregate>\Create<Aggregate>ServiceInterface;
use App\Core\Domain\Service\Security\SecurityChecker;
use App\Shared\Domain\Enum\SystemRole;
use App\Tests\Unit\Core\Domain\Mother\<Aggregate>\<Aggregate>Mother;
use PHPUnit\Framework\TestCase;

final class Create<Aggregate>HandlerTest extends TestCase
{
    public function test_GivenValidCommand_WhenInvoke_ThenServiceIsCalled(): void
    {
        $aggregate = <Aggregate>Mother::create();

        $service = $this->createMock(Create<Aggregate>ServiceInterface::class);
        $service->expects(self::once())->method('__invoke')->willReturn($aggregate);

        $checker = $this->createMock(SecurityChecker::class);

        $handler = new Create<Aggregate>Handler($service, $checker);
        $handler(new Create<Aggregate>Command(
            securityToken: new SecurityToken('admin-id', SystemRole::ADMIN),
            id: (string) $aggregate->id(),
            name: (string) $aggregate->name(),
        ));
    }
}
```

### 10.3 Handler

`src/Core/Application/Command/<Aggregate>/Create<Aggregate>/Create<Aggregate>Handler.php`:

```php
<?php

declare(strict_types=1);

namespace App\Core\Application\Command\<Aggregate>\Create<Aggregate>;

use App\Core\Application\Bus\CommandHandler;
use App\Core\Application\Command\Common\Security\SecurableHandler;
use App\Core\Application\Command\Common\Security\SecurityAwareTrait;
use App\Core\Domain\Model\VO\<Aggregate>\<Aggregate>Id;
use App\Core\Domain\Model\VO\<Aggregate>\<Aggregate>Name;
use App\Core\Domain\Service\<Aggregate>\Create<Aggregate>\Create<Aggregate>ServiceInterface;
use App\Core\Domain\Service\Security\SecurityChecker;

final readonly class Create<Aggregate>Handler implements CommandHandler, SecurableHandler
{
    use SecurityAwareTrait;

    public function __construct(
        private Create<Aggregate>ServiceInterface $service,
        private SecurityChecker $securityChecker,
    ) {}

    public function __invoke(Create<Aggregate>Command $command): void
    {
        $id = new <Aggregate>Id($command->id);

        $this->checkSecurity($command->securityToken, $id);

        ($this->service)(
            id: $id,
            name: new <Aggregate>Name($command->name),
        );
    }

    public function securityChecker(): SecurityChecker
    {
        return $this->securityChecker;
    }
}
```

```bash
git add src/Core/Application/Command/<Aggregate>/Create<Aggregate> tests/Unit/Core/Application
git commit -m "feat(<aggregate>): add Create<Aggregate> command + handler"
```

### 10.4 Repetir 10.1-10.3 para Update, Delete, etc.

---

## Paso 11 — Queries + Handlers

Por cada **lectura**:

### 11.1 Query

`src/Core/Application/Query/<Aggregate>/Get<Aggregate>/Get<Aggregate>Query.php`:

```php
<?php

declare(strict_types=1);

namespace App\Core\Application\Query\<Aggregate>\Get<Aggregate>;

use App\Core\Application\Bus\Query;
use App\Core\Application\DTO\Security\SecurityToken;

final readonly class Get<Aggregate>Query implements Query
{
    public function __construct(
        public SecurityToken $securityToken,
        public string $id,
    ) {}
}
```

### 11.2 Handler

`src/Core/Application/Query/<Aggregate>/Get<Aggregate>/Get<Aggregate>Handler.php`:

```php
<?php

declare(strict_types=1);

namespace App\Core\Application\Query\<Aggregate>\Get<Aggregate>;

use App\App\UI\API\Controller\<Aggregate>\Get<Aggregate>\Get<Aggregate>Response;
use App\Core\Application\Bus\QueryHandler;
use App\Core\Domain\Model\Repository\<Aggregate>Repository;
use App\Core\Domain\Model\VO\<Aggregate>\<Aggregate>Id;

final readonly class Get<Aggregate>Handler implements QueryHandler
{
    public function __construct(private <Aggregate>Repository $repository) {}

    public function __invoke(Get<Aggregate>Query $query): Get<Aggregate>Response
    {
        $aggregate = $this->repository->findOneOrFail(new <Aggregate>Id($query->id));
        return Get<Aggregate>Response::from($aggregate);
    }
}
```

```bash
git add src/Core/Application/Query/<Aggregate>
git commit -m "feat(<aggregate>): add queries + handlers"
```

---

## Paso 12 — Controllers + Request + Response

Por cada endpoint:

### 12.1 Request DTO

`src/App/UI/API/Controller/<Aggregate>/Create<Aggregate>/Create<Aggregate>Request.php`:

```php
<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\<Aggregate>\Create<Aggregate>;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class Create<Aggregate>Request
{
    public function __construct(
        #[Assert\NotBlank, Assert\Uuid] public string $id,
        #[Assert\NotBlank, Assert\Length(min: 2, max: 100)] public string $name,
        // ... más fields según el aggregate
    ) {}
}
```

### 12.2 Controller

`src/App/UI/API/Controller/<Aggregate>/Create<Aggregate>/Create<Aggregate>Controller.php`:

```php
<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\<Aggregate>\Create<Aggregate>;

use App\App\Auth\Domain\Service\SecurityTokenExtractorInterface;
use App\Core\Application\Bus\CommandBus;
use App\Core\Application\Command\<Aggregate>\Create<Aggregate>\Create<Aggregate>Command;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: '<Aggregate>')]
final class Create<Aggregate>Controller
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly SecurityTokenExtractorInterface $securityTokenExtractor,
    ) {}

    #[Route(path: '/<aggregates>', methods: ['POST'])]
    public function __invoke(#[MapRequestPayload] Create<Aggregate>Request $request): Response
    {
        $this->commandBus->dispatch(new Create<Aggregate>Command(
            securityToken: ($this->securityTokenExtractor)(),
            id: $request->id,
            name: $request->name,
        ));

        return new Response(status: Response::HTTP_CREATED);
    }
}
```

### 12.3 Response DTO (para queries)

`src/App/UI/API/Controller/<Aggregate>/Get<Aggregate>/Get<Aggregate>Response.php`:

```php
<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\<Aggregate>\Get<Aggregate>;

use App\Core\Domain\Model\Aggregate\<Aggregate>;

final readonly class Get<Aggregate>Response
{
    public function __construct(
        public string $id,
        public string $name,
        public string $createdAt,
    ) {}

    public static function from(<Aggregate> $aggregate): self
    {
        return new self(
            id: (string) $aggregate->id(),
            name: (string) $aggregate->name(),
            createdAt: $aggregate->createdAt()->format(DATE_ATOM),
        );
    }
}
```

### 12.4 Controller de query

```php
#[Route(path: '/<aggregates>/{id}', methods: ['GET'])]
public function __invoke(string $id): Response
{
    /** @var Get<Aggregate>Response $response */
    $response = $this->queryBus->ask(new Get<Aggregate>Query(
        securityToken: ($this->securityTokenExtractor)(),
        id: $id,
    ));

    return new JsonResponse($response);
}
```

```bash
git add src/App/UI/API/Controller/<Aggregate>
git commit -m "feat(<aggregate>): add controllers + request/response DTOs"
```

---

## Paso 13 — Mothers (helpers de tests)

`tests/Unit/Core/Domain/Mother/<Aggregate>/<Aggregate>IdMother.php`:

```php
<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Mother\<Aggregate>;

use App\Core\Domain\Model\VO\<Aggregate>\<Aggregate>Id;

final class <Aggregate>IdMother
{
    public static function create(?string $value = null): <Aggregate>Id
    {
        return new <Aggregate>Id($value ?? <Aggregate>Id::generate()->__toString());
    }
}
```

`tests/Unit/Core/Domain/Mother/<Aggregate>/<Aggregate>NameMother.php`:

```php
<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Mother\<Aggregate>;

use App\Core\Domain\Model\VO\<Aggregate>\<Aggregate>Name;
use Faker\Factory;

final class <Aggregate>NameMother
{
    public static function create(?string $value = null): <Aggregate>Name
    {
        return new <Aggregate>Name($value ?? Factory::create()->company());
    }
}
```

`tests/Unit/Core/Domain/Mother/<Aggregate>/<Aggregate>Mother.php`:

```php
<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Mother\<Aggregate>;

use App\Core\Domain\Model\Aggregate\<Aggregate>;
use App\Core\Domain\Model\VO\<Aggregate>\<Aggregate>Id;
use App\Core\Domain\Model\VO\<Aggregate>\<Aggregate>Name;

final class <Aggregate>Mother
{
    public static function create(
        ?<Aggregate>Id $id = null,
        ?<Aggregate>Name $name = null,
    ): <Aggregate> {
        return <Aggregate>::create(
            id: $id ?? <Aggregate>IdMother::create(),
            name: $name ?? <Aggregate>NameMother::create(),
        );
    }
}
```

```bash
git add tests/Unit/Core/Domain/Mother/<Aggregate>
git commit -m "test(<aggregate>): add Mothers"
```

---

## Paso 14 — Convivencia legacy + verificar

> ⚠️ **CAMBIO RESPECTO A LA VERSIÓN ANTERIOR**: las versiones iniciales de este recipe instruían a borrar el código legacy dentro del slice. Esa estrategia rompe el sistema porque los aggregates legacy se referencian unos a otros (Client→Sector, ProjectUser→User+Project+ProjectRole, etc.) y borrar uno mientras los demás siguen activos hace que el legacy no compile.
>
> **Nueva regla**: cada slice **solo CREA código nuevo** en `src/Core/...` y `src/App/UI/...`. El legacy se queda intacto durante toda la fase de Lotes A-D. La eliminación se hace **toda junta** en el Plan 8 (cleanup), en orden seguro y atómico.

### 14.1 Asegurar la convivencia sin doble mapping Doctrine

Doctrine no tolera que dos entidades mapeen la misma tabla. Para que el nuevo `App\Core\Domain\Model\Aggregate\<Aggregate>` y el legacy `App\<Context>\Domain\<Aggregate>` coexistan, hay que **excluir el legacy del auto-mapping** de Doctrine SIN borrar los archivos PHP.

En `config/packages/doctrine.yaml`, asegúrate de que el mapping `App` ya NO usa `auto_mapping: true` ni `dir: '%kernel.project_dir%/src'`. En su lugar, listar explícitamente solo los contextos legacy que aún se quieren mapear, EXCLUYENDO el aggregate que acaba de migrarse:

```yaml
doctrine:
    orm:
        auto_mapping: false
        mappings:
            # Mappings legacy (attribute-based) — se irán quitando entrada a entrada
            UserManagementLegacy:
                type: attribute
                is_bundle: false
                dir: '%kernel.project_dir%/src/UserManagement/Domain'
                prefix: 'App\UserManagement\Domain'
                alias: UserManagementLegacy
            ClientManagementLegacy:
                type: attribute
                is_bundle: false
                dir: '%kernel.project_dir%/src/ClientManagement/Domain'
                prefix: 'App\ClientManagement\Domain'
                alias: ClientManagementLegacy
            # ... etc ...

            # Mapping nuevo (XML) — recibe TODOS los aggregates migrados
            Core:
                is_bundle: false
                type: xml
                dir: '%kernel.project_dir%/src/Core/Infrastructure/Persistence/Doctrine/ORM/Mapping/XML'
                prefix: 'App\Core\Domain\Model\Aggregate'
                alias: Core
```

Cuando un aggregate migra (por ejemplo Sector), su entrada legacy correspondiente (`ClientManagementLegacy/Sector*`) debe **desaparecer del scope del mapping** sin borrar el archivo PHP. La forma más sencilla: el agente del slice mueve el archivo legacy del aggregate concreto (ej. `src/ClientManagement/Domain/Sector/Sector.php`) a `src/ClientManagement/Domain/_legacy_unmapped/Sector.php` o similar, fuera del `dir:` configurado. Ese movimiento se debe coordinar para que el código legacy que aún typehints `App\ClientManagement\Domain\Sector\Sector` siga compilando — lo más cómodo es **dejar la clase exactamente donde está, pero borrar el `#[ORM\Entity]` attribute** mediante un parche pequeño documentado en el slice. La clase entonces queda como una clase PHP normal: los typehints siguen funcionando, pero Doctrine no la mapea.

> Recomendación: en el primer slice de cada contexto legacy, hacer el cambio "auto_mapping → mapping explícito" en `doctrine.yaml` una sola vez. Los slices siguientes solo borran annotations `#[ORM\Entity]` del legacy correspondiente.

### 14.2 Conflicto de rutas

Si tu nuevo controller en `App\App\UI\API\Controller\<Aggregate>\<Action>\` declara `#[Route(path: '/<aggregates>', methods: ['POST'])]` y el legacy controller en `<Context>/Infrastructure/Http/` tiene la misma ruta, Symfony detecta colisión.

**Solución**: en el slice, **comenta** el `#[Route(...)]` del controller legacy (sin borrar el archivo). Añade un comentario `// MIGRATED to App\\App\\UI\\API\\Controller\\<Aggregate>` arriba para trazabilidad. El controller legacy queda como código muerto sin route attached — no responde a HTTP, pero el archivo PHP existe.

### 14.3 Smoke test manual

```bash
# Crear via API (usa el NUEVO controller)
curl -X POST http://localhost/<aggregates> \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $JWT" \
  -d '{"id":"00000000-0000-4000-8000-000000000001","name":"Test"}'
# Expected: 201

# Leer
curl http://localhost/<aggregates>/00000000-0000-4000-8000-000000000001 \
  -H "Authorization: Bearer $JWT"
# Expected: 200 con JSON
```

### 14.4 Tests verdes

```bash
make tests-unit
make composer ARGS="phpstan"
make bin-console ARGS="doctrine:schema:validate"
```

Si `doctrine:schema:validate` reporta error de doble mapping para tu aggregate, vuelve al paso 14.1 — la annotation `#[ORM\Entity]` legacy sigue activa o el legacy sigue dentro del `dir:` mapeado.

### 14.5 Commit final del slice

```bash
git commit -m "feat(<aggregate>): complete vertical slice migration"
```

El borrado físico de los archivos legacy (carpetas `<Context>/...`) ocurre en el **Plan 8 (cleanup)** cuando TODOS los aggregates están migrados y nadie referencia el legacy.

---

## Notas finales

- **Naming conventions** (cap. 08):
  - VOs: `final readonly` + `__toString()` + `equals(self $o)`
  - Aggregates: NO final (Doctrine necesita proxies), constructor private/protected, factory pública
  - Repositorios: interfaz sin sufijo `Interface`; impl con prefijo `Orm`
  - Services: interfaz con sufijo `Interface`; impl readonly
  - Tests: nombres `test_GivenX_WhenY_ThenZ` snake_case

- **Errores comunes a evitar** (cap. 10):
  - No mapear el mismo aggregate dos veces (asegurar borrado legacy antes de añadir XML nuevo)
  - No usar `\Exception` ni `\DomainException` — siempre `CustomException`
  - No exponer setters en aggregates — usar mutadores con nombre de negocio
  - No olvidar `recordEvent()` en cada mutador
  - Validar `doctrine:schema:validate` tras cada XML
