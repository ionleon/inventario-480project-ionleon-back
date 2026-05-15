<?php

declare(strict_types=1);

namespace App\Core\Application\Command\Contact\UpdateContact;

use App\Core\Application\Bus\CommandHandler;
use App\Core\Application\Command\Common\Security\SecurableHandler;
use App\Core\Application\Command\Common\Security\SecurityAwareTrait;
use App\Core\Domain\Model\VO\Common\Email;
use App\Core\Domain\Model\VO\Common\Phone;
use App\Core\Domain\Model\VO\Contact\ContactId;
use App\Core\Domain\Model\VO\Contact\ContactName;
use App\Core\Domain\Service\Contact\UpdateContact\UpdateContactServiceInterface;
use App\Core\Domain\Service\Security\SecurityChecker;

final readonly class UpdateContactHandler implements CommandHandler, SecurableHandler
{
    use SecurityAwareTrait;

    public function __construct(
        private UpdateContactServiceInterface $service,
        private SecurityChecker $securityChecker,
    ) {}

    public function __invoke(UpdateContactCommand $command): void
    {
        $id = new ContactId($command->id);

        $this->checkSecurity($command->securityToken, $id);

        ($this->service)(
            id: $id,
            fullName: new ContactName($command->fullName),
            email: new Email($command->email),
            phoneNumber: new Phone($command->phoneNumber),
            note: $command->note,
        );
    }

    public function securityChecker(): SecurityChecker
    {
        return $this->securityChecker;
    }
}
