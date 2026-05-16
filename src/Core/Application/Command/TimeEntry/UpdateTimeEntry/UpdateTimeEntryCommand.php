<?php

declare(strict_types=1);

namespace App\Core\Application\Command\TimeEntry\UpdateTimeEntry;

use App\Core\Application\Bus\Command;
use App\Core\Domain\DTO\Security\SecurityToken;

final readonly class UpdateTimeEntryCommand implements Command
{
    public function __construct(
        public SecurityToken $securityToken,
        public string $id,
        public string $date,
        public string $hours,
        public ?string $description,
    ) {}
}
