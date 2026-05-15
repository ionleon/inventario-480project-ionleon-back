<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Application\Command\Technology;

use App\Core\Application\Command\Technology\DeleteTechnology\DeleteTechnologyCommand;
use App\Core\Application\Command\Technology\DeleteTechnology\DeleteTechnologyHandler;
use App\Core\Domain\DTO\Security\SecurityToken;
use App\Core\Domain\Service\Technology\DeleteTechnology\DeleteTechnologyServiceInterface;
use App\Core\Domain\Service\Security\SecurityChecker;
use App\Shared\Domain\Enum\SystemRole;
use App\Tests\Unit\Core\Domain\Mother\Technology\TechnologyMother;
use PHPUnit\Framework\TestCase;

final class DeleteTechnologyHandlerTest extends TestCase
{
    public function test_GivenValidCommand_WhenInvoke_ThenServiceIsCalled(): void
    {
        $technology = TechnologyMother::create();

        $service = $this->createMock(DeleteTechnologyServiceInterface::class);
        $service->expects(self::once())->method('__invoke');

        $checker = $this->createMock(SecurityChecker::class);

        $handler = new DeleteTechnologyHandler($service, $checker);
        $handler(new DeleteTechnologyCommand(
            securityToken: new SecurityToken('admin-id', SystemRole::ADMIN),
            id: (string) $technology->id(),
        ));
    }
}
