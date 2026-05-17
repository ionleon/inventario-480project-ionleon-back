<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\ProjectUser\RemoveUserFromProject;

use App\Core\Domain\Exception\ProjectUser\ProjectUserNotFoundException;
use App\Core\Domain\Model\Repository\ProjectUserRepository;
use App\Core\Domain\Model\VO\ProjectUser\ProjectUserId;

final readonly class RemoveUserFromProjectService implements RemoveUserFromProjectServiceInterface
{
    public function __construct(
        private ProjectUserRepository $projectUserRepository,
    ) {
    }

    /** @throws ProjectUserNotFoundException */
    public function __invoke(ProjectUserId $id): void
    {
        $projectUser = $this->projectUserRepository->findOneOrFail($id);
        $projectUser->remove();
        $this->projectUserRepository->remove($projectUser);
    }
}
