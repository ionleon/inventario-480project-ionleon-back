<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\ProjectUser\ToggleProjectUserActivation;

use App\Core\Domain\Model\Aggregate\ProjectUser;
use App\Core\Domain\Model\Repository\ProjectUserRepository;
use App\Core\Domain\Model\VO\ProjectUser\ProjectUserId;

final readonly class ToggleProjectUserActivationService implements ToggleProjectUserActivationServiceInterface
{
    public function __construct(
        private ProjectUserRepository $projectUserRepository,
    ) {}

    public function __invoke(ProjectUserId $id): ProjectUser
    {
        $projectUser = $this->projectUserRepository->findOneOrFail($id);
        $projectUser->toggleActivation();
        return $projectUser;
    }
}
