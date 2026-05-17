<?php

declare(strict_types=1);

namespace App\Core\Application\Command\TimeEntry\CreateTimeEntry;

use App\Core\Application\Bus\Command;
use App\Core\Domain\DTO\Security\SecurityToken;

final readonly class CreateTimeEntryCommand implements Command
{
    public function __construct(
        public SecurityToken $securityToken,
        public string $id,
        public string $projectId,
        public string $userId,
        public string $date,
        public string $hours,
        public ?string $description,
    ) {
    }
}
