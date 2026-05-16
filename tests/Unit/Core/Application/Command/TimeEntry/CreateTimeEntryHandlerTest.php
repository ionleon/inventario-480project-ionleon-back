<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Application\Command\TimeEntry;

use App\Core\Application\Command\TimeEntry\CreateTimeEntry\CreateTimeEntryCommand;
use App\Core\Application\Command\TimeEntry\CreateTimeEntry\CreateTimeEntryHandler;
use App\Core\Domain\DTO\Security\SecurityToken;
use App\Core\Domain\Service\TimeEntry\CreateTimeEntry\CreateTimeEntryServiceInterface;
use App\Shared\Domain\Enum\SystemRole;
use App\Tests\Unit\Core\Domain\Mother\Project\ProjectIdMother;
use App\Tests\Unit\Core\Domain\Mother\TimeEntry\TimeEntryIdMother;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class CreateTimeEntryHandlerTest extends TestCase
{
    public function test_GivenValidCommand_WhenInvoke_ThenServiceIsCalled(): void
    {
        $service = $this->createMock(CreateTimeEntryServiceInterface::class);
        $service->expects(self::once())->method('__invoke');

        $handler = new CreateTimeEntryHandler($service);
        $handler(new CreateTimeEntryCommand(
            securityToken: new SecurityToken('admin-id', SystemRole::ADMIN),
            id: (string) TimeEntryIdMother::create(),
            projectId: (string) ProjectIdMother::create(),
            userId: Uuid::v4()->toRfc4122(),
            date: '2026-05-15',
            hours: '8.00',
            description: null,
        ));
    }
}
