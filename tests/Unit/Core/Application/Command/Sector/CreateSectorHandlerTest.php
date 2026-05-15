<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Application\Command\Sector;

use App\Core\Application\Command\Sector\CreateSector\CreateSectorCommand;
use App\Core\Application\Command\Sector\CreateSector\CreateSectorHandler;
use App\Core\Domain\DTO\Security\SecurityToken;
use App\Core\Domain\Service\Sector\CreateSector\CreateSectorServiceInterface;
use App\Core\Domain\Service\Security\SecurityChecker;
use App\Shared\Domain\Enum\SystemRole;
use App\Tests\Unit\Core\Domain\Mother\Sector\SectorMother;
use PHPUnit\Framework\TestCase;

final class CreateSectorHandlerTest extends TestCase
{
    public function test_GivenValidCommand_WhenInvoke_ThenServiceIsCalled(): void
    {
        $sector = SectorMother::create();

        $service = $this->createMock(CreateSectorServiceInterface::class);
        $service->expects(self::once())->method('__invoke')->willReturn($sector);

        $checker = $this->createMock(SecurityChecker::class);

        $handler = new CreateSectorHandler($service, $checker);
        $handler(new CreateSectorCommand(
            securityToken: new SecurityToken('admin-id', SystemRole::ADMIN),
            id: (string) $sector->id(),
            name: (string) $sector->name(),
        ));
    }
}
