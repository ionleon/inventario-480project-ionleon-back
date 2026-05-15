<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Application\Command\Contact;

use App\Core\Application\Command\Contact\CreateContact\CreateContactCommand;
use App\Core\Application\Command\Contact\CreateContact\CreateContactHandler;
use App\Core\Domain\DTO\Security\SecurityToken;
use App\Core\Domain\Service\Contact\CreateContact\CreateContactServiceInterface;
use App\Core\Domain\Service\Security\SecurityChecker;
use App\Shared\Domain\Enum\SystemRole;
use App\Tests\Unit\Core\Domain\Mother\Client\ClientIdMother;
use App\Tests\Unit\Core\Domain\Mother\Contact\ContactIdMother;
use PHPUnit\Framework\TestCase;

final class CreateContactHandlerTest extends TestCase
{
    public function test_GivenValidCommand_WhenInvoke_ThenServiceIsCalled(): void
    {
        $service = $this->createMock(CreateContactServiceInterface::class);
        $service->expects(self::once())->method('__invoke');

        $checker = $this->createMock(SecurityChecker::class);

        $handler = new CreateContactHandler($service, $checker);
        $handler(new CreateContactCommand(
            securityToken: new SecurityToken('admin-id', SystemRole::ADMIN),
            id: (string) ContactIdMother::create(),
            clientId: (string) ClientIdMother::create(),
            fullName: 'John Doe',
            email: 'john@example.com',
            phoneNumber: '+34600000000',
            note: null,
        ));
    }
}
