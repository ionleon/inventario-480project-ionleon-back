<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\User\ToggleUserActivation;

use App\Core\Domain\Model\VO\User\UserId;

interface ToggleUserActivationServiceInterface
{
    public function __invoke(UserId $id): void;
}
