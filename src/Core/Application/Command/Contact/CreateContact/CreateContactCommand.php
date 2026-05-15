<?php

declare(strict_types=1);

namespace App\Core\Application\Command\Contact\CreateContact;

use App\Core\Application\Bus\Command;
use App\Core\Domain\DTO\Security\SecurityToken;

final readonly class CreateContactCommand implements Command
{
    public function __construct(
        public SecurityToken $securityToken,
        public string $id,
        public string $clientId,
        public string $fullName,
        public string $email,
        public string $phoneNumber,
        public ?string $note,
    ) {}
}
