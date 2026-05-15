<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Mother\ProjectUser;

use App\Core\Domain\Model\VO\ProjectUser\ProjectUserId;

final class ProjectUserIdMother
{
    public static function create(): ProjectUserId
    {
        return ProjectUserId::generate();
    }

    public static function fromString(string $id): ProjectUserId
    {
        return new ProjectUserId($id);
    }
}
