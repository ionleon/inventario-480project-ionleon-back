<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\ProjectRole\DeleteProjectRole;

use App\Core\Domain\Exception\ProjectRole\ProjectRoleNotFoundException;
use App\Core\Domain\Model\Repository\ProjectRoleRepository;
use App\Core\Domain\Model\VO\ProjectRole\ProjectRoleId;

final readonly class DeleteProjectRoleService implements DeleteProjectRoleServiceInterface
{
    // TODO (Plan 5 — ProjectUser slice): Add cross-aggregate check here.
    // Before removing, verify no ProjectUser references this ProjectRole. If any
    // ProjectUser exists with this project_role_id, throw a DomainException blocking deletion.
    // The check will be implemented via the ProjectUserRepository once ProjectUser is migrated.

    public function __construct(private ProjectRoleRepository $repository) {}

    /** @throws ProjectRoleNotFoundException */
    public function __invoke(ProjectRoleId $id): void
    {
        $projectRole = $this->repository->findOneOrFail($id);

        $this->repository->remove($projectRole);
    }
}
