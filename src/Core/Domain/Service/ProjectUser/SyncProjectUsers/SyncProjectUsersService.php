<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\ProjectUser\SyncProjectUsers;

use App\Core\Domain\Model\Aggregate\ProjectUser;
use App\Core\Domain\Model\Repository\ProjectRepository;
use App\Core\Domain\Model\Repository\ProjectRoleRepository;
use App\Core\Domain\Model\Repository\ProjectUserRepository;
use App\Core\Domain\Model\Repository\UserRepository;
use App\Core\Domain\Model\VO\Project\ProjectId;
use App\Core\Domain\Model\VO\ProjectRole\ProjectRoleId;
use App\Core\Domain\Model\VO\ProjectUser\ProjectUserAllocation;
use App\Core\Domain\Model\VO\ProjectUser\ProjectUserId;
use App\Core\Domain\Model\VO\User\UserId;

final readonly class SyncProjectUsersService implements SyncProjectUsersServiceInterface
{
    public function __construct(
        private ProjectUserRepository $projectUserRepository,
        private ProjectRepository $projectRepository,
        private UserRepository $userRepository,
        private ProjectRoleRepository $projectRoleRepository,
    ) {}

    /**
     * @param list<array{userId: string, roleId: string, allocation: int}> $users
     */
    public function __invoke(ProjectId $projectId, array $users): void
    {
        $this->projectRepository->findOneOrFail($projectId);

        // Index current assignments by userId string
        $current = [];
        foreach ($this->projectUserRepository->findByProject($projectId) as $pu) {
            $current[(string) $pu->userId()] = $pu;
        }

        $incomingUserIds = [];

        foreach ($users as $data) {
            $userId = $data['userId'];
            $roleId = new ProjectRoleId($data['roleId']);
            $allocation = new ProjectUserAllocation($data['allocation'] ?? 0);

            $this->userRepository->findOneOrFail(new UserId($userId));
            $this->projectRoleRepository->findOneOrFail($roleId);

            $incomingUserIds[] = $userId;

            if (isset($current[$userId])) {
                /** @var ProjectUser $existing */
                $existing = $current[$userId];
                $existing->update($roleId, $allocation);
                $existing->activate();
            } else {
                $pu = ProjectUser::assign(
                    ProjectUserId::generate(),
                    $projectId,
                    new UserId($userId),
                    $roleId,
                    $allocation,
                );
                $this->projectUserRepository->add($pu);
            }
        }

        // Deactivate assignments not in the incoming list
        foreach ($current as $userIdStr => $existing) {
            if (!in_array($userIdStr, $incomingUserIds, true)) {
                $existing->deactivate();
            }
        }
    }
}
