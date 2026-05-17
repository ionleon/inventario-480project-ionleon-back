<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\Project\ToggleProjectActivation;

use App\Core\Domain\Exception\Project\ProjectNotFoundException;
use App\Core\Domain\Model\Repository\ProjectRepository;
use App\Core\Domain\Model\VO\Project\ProjectId;

final readonly class ToggleProjectActivationService implements ToggleProjectActivationServiceInterface
{
    public function __construct(private ProjectRepository $repository)
    {
    }

    /** @throws ProjectNotFoundException */
    public function __invoke(ProjectId $id): void
    {
        $project = $this->repository->findOneOrFail($id);
        $project->toggleActivation();
    }
}
