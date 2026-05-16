<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\ProjectUser\RemoveUserFromProject;

use App\Core\Domain\Exception\ProjectUser\ProjectUserNotFoundException;
use App\Core\Domain\Model\VO\ProjectUser\ProjectUserId;

interface RemoveUserFromProjectServiceInterface
{
    /** @throws ProjectUserNotFoundException */
    public function __invoke(ProjectUserId $id): void;
}
