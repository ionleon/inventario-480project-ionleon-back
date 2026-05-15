<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Mother\Project;

use App\Core\Domain\Model\Aggregate\Project;
use App\Core\Domain\Model\VO\Client\ClientId;
use App\Core\Domain\Model\VO\Project\ProjectDescription;
use App\Core\Domain\Model\VO\Project\ProjectEndDate;
use App\Core\Domain\Model\VO\Project\ProjectId;
use App\Core\Domain\Model\VO\Project\ProjectName;
use App\Core\Domain\Model\VO\Project\ProjectStartDate;
use App\Core\Domain\Model\VO\User\UserId;
use App\Tests\Unit\Core\Domain\Mother\Client\ClientIdMother;
use App\Tests\Unit\Core\Domain\Mother\User\UserIdMother;

final class ProjectMother
{
    public static function create(
        ?ProjectId $id = null,
        ?ProjectName $name = null,
        ?ProjectDescription $description = null,
        ?ClientId $clientId = null,
        ?UserId $managerId = null,
        ?ProjectStartDate $startDate = null,
        ?ProjectEndDate $endDate = null,
        array $technologies = [],
    ): Project {
        return Project::create(
            id: $id ?? ProjectIdMother::create(),
            name: $name ?? ProjectNameMother::create(),
            description: $description,
            clientId: $clientId ?? ClientIdMother::create(),
            managerId: $managerId ?? UserIdMother::create(),
            technologies: $technologies,
            startDate: $startDate ?? ProjectStartDateMother::create(),
            endDate: $endDate,
        );
    }
}
