<?php

declare(strict_types=1);

namespace App\Core\Application\Command\Contact\UpdateContact;

use App\Core\Application\Bus\Command;
use App\Core\Domain\DTO\Security\SecurityToken;

final readonly class UpdateContactCommand implements Command
{
    public function __construct(
        public SecurityToken $securityToken,
        public string $id,
        public string $fullName,
        public string $email,
        public string $phoneNumber,
        public ?string $note,
    ) {}
}
