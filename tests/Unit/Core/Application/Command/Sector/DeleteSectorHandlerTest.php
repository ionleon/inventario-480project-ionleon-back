<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Application\Command\Sector;

use App\Core\Application\Command\Sector\DeleteSector\DeleteSectorCommand;
use App\Core\Application\Command\Sector\DeleteSector\DeleteSectorHandler;
use App\Core\Domain\DTO\Security\SecurityToken;
use App\Core\Domain\Service\Sector\DeleteSector\DeleteSectorServiceInterface;
use App\Core\Domain\Service\Security\SecurityChecker;
use App\Shared\Domain\Enum\SystemRole;
use App\Tests\Unit\Core\Domain\Mother\Sector\SectorIdMother;
use PHPUnit\Framework\TestCase;

final class DeleteSectorHandlerTest extends TestCase
{
    public function test_GivenValidCommand_WhenInvoke_ThenServiceIsCalled(): void
    {
        $id = SectorIdMother::create();

        $service = $this->createMock(DeleteSectorServiceInterface::class);
        $service->expects(self::once())->method('__invoke');

        $checker = $this->createMock(SecurityChecker::class);

        $handler = new DeleteSectorHandler($service, $checker);
        $handler(new DeleteSectorCommand(
            securityToken: new SecurityToken('admin-id', SystemRole::ADMIN),
            id: (string) $id,
        ));
    }
}
