<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\ProjectUser\AssignUserToProject;

use App\Core\Domain\Exception\ProjectUser\DuplicatedProjectUserException;
use App\Core\Domain\Model\Aggregate\ProjectUser;
use App\Core\Domain\Model\Repository\ProjectRepository;
use App\Core\Domain\Model\Repository\ProjectUserRepository;
use App\Core\Domain\Model\Repository\UserRepository;
use App\Core\Domain\Model\Repository\ProjectRoleRepository;
use App\Core\Domain\Model\VO\Project\ProjectId;
use App\Core\Domain\Model\VO\ProjectRole\ProjectRoleId;
use App\Core\Domain\Model\VO\ProjectUser\ProjectUserAllocation;
use App\Core\Domain\Model\VO\ProjectUser\ProjectUserId;
use App\Core\Domain\Model\VO\User\UserId;

final readonly class AssignUserToProjectService implements AssignUserToProjectServiceInterface
{
    public function __construct(
        private ProjectUserRepository $projectUserRepository,
        private ProjectRepository $projectRepository,
        private UserRepository $userRepository,
        private ProjectRoleRepository $projectRoleRepository,
    ) {
    }

    public function __invoke(
        ProjectUserId $id,
        ProjectId $projectId,
        UserId $userId,
        ProjectRoleId $roleId,
        ProjectUserAllocation $allocation,
    ): ProjectUser {
        $this->projectRepository->findOneOrFail($projectId);
        $this->userRepository->findOneOrFail($userId);
        $this->projectRoleRepository->findOneOrFail($roleId);

        $existing = $this->projectUserRepository->findOneByProjectAndUser($projectId, $userId);

        if ($existing !== null) {
            if ($existing->isActive()) {
                throw new DuplicatedProjectUserException();
            }
            // Reactivate the inactive assignment
            $existing->activate();
            $existing->update($roleId, $allocation);
            return $existing;
        }

        $projectUser = ProjectUser::assign($id, $projectId, $userId, $roleId, $allocation);
        $this->projectUserRepository->add($projectUser);

        return $projectUser;
    }
}
