<?php

declare(strict_types=1);

namespace App\Core\Application\Command\Project\CreateProject;

use App\Core\Application\Bus\Command;
use App\Core\Domain\DTO\Security\SecurityToken;

final readonly class CreateProjectCommand implements Command
{
    /**
     * @param list<string> $technologyIds
     */
    public function __construct(
        public SecurityToken $securityToken,
        public string $id,
        public string $name,
        public ?string $description,
        public string $clientId,
        public string $managerId,
        public array $technologyIds,
        public ?string $startDate,
        public ?string $endDate,
    ) {}
}
