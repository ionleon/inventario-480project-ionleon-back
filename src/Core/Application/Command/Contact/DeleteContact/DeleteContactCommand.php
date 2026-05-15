<?php

declare(strict_types=1);

namespace App\Core\Application\Command\Contact\DeleteContact;

use App\Core\Application\Bus\Command;
use App\Core\Domain\DTO\Security\SecurityToken;

final readonly class DeleteContactCommand implements Command
{
    public function __construct(
        public SecurityToken $securityToken,
        public string $id,
    ) {}
}
