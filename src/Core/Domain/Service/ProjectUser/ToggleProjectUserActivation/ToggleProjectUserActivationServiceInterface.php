<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\ProjectUser\ToggleProjectUserActivation;

use App\Core\Domain\Model\Aggregate\ProjectUser;
use App\Core\Domain\Model\VO\ProjectUser\ProjectUserId;

interface ToggleProjectUserActivationServiceInterface
{
    public function __invoke(ProjectUserId $id): ProjectUser;
}
