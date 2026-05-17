<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\ProjectUser\UpdateProjectUser;

use App\Core\Domain\Model\Aggregate\ProjectUser;
use App\Core\Domain\Model\Repository\ProjectRoleRepository;
use App\Core\Domain\Model\Repository\ProjectUserRepository;
use App\Core\Domain\Model\VO\ProjectRole\ProjectRoleId;
use App\Core\Domain\Model\VO\ProjectUser\ProjectUserAllocation;
use App\Core\Domain\Model\VO\ProjectUser\ProjectUserId;

final readonly class UpdateProjectUserService implements UpdateProjectUserServiceInterface
{
    public function __construct(
        private ProjectUserRepository $projectUserRepository,
        private ProjectRoleRepository $projectRoleRepository,
    ) {
    }

    public function __invoke(
        ProjectUserId $id,
        ProjectRoleId $roleId,
        ProjectUserAllocation $allocation,
    ): ProjectUser {
        $projectUser = $this->projectUserRepository->findOneOrFail($id);
        $this->projectRoleRepository->findOneOrFail($roleId);

        $projectUser->update($roleId, $allocation);

        return $projectUser;
    }
}
