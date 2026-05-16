<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\Project\ToggleProjectActivation;

use App\Core\Domain\Exception\Project\ProjectNotFoundException;
use App\Core\Domain\Model\VO\Project\ProjectId;

interface ToggleProjectActivationServiceInterface
{
    /** @throws ProjectNotFoundException */
    public function __invoke(ProjectId $id): void;
}
