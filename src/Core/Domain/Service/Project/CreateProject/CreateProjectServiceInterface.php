<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\Project\CreateProject;

use App\Core\Domain\Exception\Client\ClientNotFoundException;
use App\Core\Domain\Exception\Project\DuplicatedProjectNameException;
use App\Core\Domain\Exception\Project\InvalidProjectDateRangeException;
use App\Core\Domain\Exception\Technology\TechnologyNotFoundException;
use App\Core\Domain\Exception\User\UserNotFoundException;
use App\Core\Domain\Model\Aggregate\Project;
use App\Core\Domain\Model\VO\Client\ClientId;
use App\Core\Domain\Model\VO\Project\ProjectDescription;
use App\Core\Domain\Model\VO\Project\ProjectEndDate;
use App\Core\Domain\Model\VO\Project\ProjectId;
use App\Core\Domain\Model\VO\Project\ProjectName;
use App\Core\Domain\Model\VO\Project\ProjectStartDate;
use App\Core\Domain\Model\VO\Technology\TechnologyId;
use App\Core\Domain\Model\VO\User\UserId;

interface CreateProjectServiceInterface
{
    /**
     * @param list<TechnologyId> $technologyIds
     *
     * @throws DuplicatedProjectNameException
     * @throws ClientNotFoundException
     * @throws UserNotFoundException
     * @throws TechnologyNotFoundException
     * @throws InvalidProjectDateRangeException
     */
    public function __invoke(
        ProjectId $id,
        ProjectName $name,
        ?ProjectDescription $description,
        ClientId $clientId,
        UserId $managerId,
        array $technologyIds,
        ?ProjectStartDate $startDate,
        ?ProjectEndDate $endDate,
    ): Project;
}
