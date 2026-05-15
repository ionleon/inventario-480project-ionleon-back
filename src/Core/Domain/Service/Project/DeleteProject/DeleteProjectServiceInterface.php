<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\Project\DeleteProject;

use App\Core\Domain\Exception\Project\ProjectNotFoundException;
use App\Core\Domain\Model\VO\Project\ProjectId;

interface DeleteProjectServiceInterface
{
    /** @throws ProjectNotFoundException */
    public function __invoke(ProjectId $id): void;
}
