<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Application\Command\Technology;

use App\Core\Application\Command\Technology\CreateTechnology\CreateTechnologyCommand;
use App\Core\Application\Command\Technology\CreateTechnology\CreateTechnologyHandler;
use App\Core\Domain\DTO\Security\SecurityToken;
use App\Core\Domain\Service\Technology\CreateTechnology\CreateTechnologyServiceInterface;
use App\Core\Domain\Service\Security\SecurityChecker;
use App\Shared\Domain\Enum\SystemRole;
use App\Tests\Unit\Core\Domain\Mother\Technology\TechnologyMother;
use PHPUnit\Framework\TestCase;

final class CreateTechnologyHandlerTest extends TestCase
{
    public function test_GivenValidCommand_WhenInvoke_ThenServiceIsCalled(): void
    {
        $technology = TechnologyMother::create();

        $service = $this->createMock(CreateTechnologyServiceInterface::class);
        $service->expects(self::once())->method('__invoke')->willReturn($technology);

        $checker = $this->createMock(SecurityChecker::class);

        $handler = new CreateTechnologyHandler($service, $checker);
        $handler(new CreateTechnologyCommand(
            securityToken: new SecurityToken('admin-id', SystemRole::ADMIN),
            id: (string) $technology->id(),
            name: (string) $technology->name(),
        ));
    }
}
