<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\ProjectRole\ListProjectRoles;

use App\Core\Domain\Model\Aggregate\ProjectRole;

final readonly class ListProjectRolesResponse
{
    /** @param list<array{id: string, name: string}> $projectRoles */
    public function __construct(
        public array $projectRoles,
    ) {
    }

    /** @param list<ProjectRole> $projectRoles */
    public static function from(array $projectRoles): self
    {
        return new self(
            projectRoles: array_map(
                static fn(ProjectRole $r) => [
                    'id' => (string) $r->id(),
                    'name' => (string) $r->name(),
                ],
                $projectRoles,
            ),
        );
    }
}
