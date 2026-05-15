<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\Project\CreateProject;

use App\Core\Domain\Exception\Project\DuplicatedProjectNameException;
use App\Core\Domain\Model\Aggregate\Project;
use App\Core\Domain\Model\Repository\ClientRepository;
use App\Core\Domain\Model\Repository\ProjectRepository;
use App\Core\Domain\Model\Repository\TechnologyRepository;
use App\Core\Domain\Model\Repository\UserRepository;
use App\Core\Domain\Model\VO\Client\ClientId;
use App\Core\Domain\Model\VO\Project\ProjectDescription;
use App\Core\Domain\Model\VO\Project\ProjectEndDate;
use App\Core\Domain\Model\VO\Project\ProjectId;
use App\Core\Domain\Model\VO\Project\ProjectName;
use App\Core\Domain\Model\VO\Project\ProjectStartDate;
use App\Core\Domain\Model\VO\Technology\TechnologyId;
use App\Core\Domain\Model\VO\User\UserId;

final readonly class CreateProjectService implements CreateProjectServiceInterface
{
    public function __construct(
        private ProjectRepository $projectRepository,
        private ClientRepository $clientRepository,
        private UserRepository $userRepository,
        private TechnologyRepository $technologyRepository,
    ) {}

    public function __invoke(
        ProjectId $id,
        ProjectName $name,
        ?ProjectDescription $description,
        ClientId $clientId,
        UserId $managerId,
        array $technologyIds,
        ?ProjectStartDate $startDate,
        ?ProjectEndDate $endDate,
    ): Project {
        if ($this->projectRepository->findOneByName($name) !== null) {
            throw new DuplicatedProjectNameException((string) $name);
        }

        $this->clientRepository->findOneOrFail($clientId);
        $this->userRepository->findOneOrFail($managerId);

        $technologies = array_map(
            fn(TechnologyId $tid) => $this->technologyRepository->findOneOrFail($tid),
            $technologyIds,
        );

        $project = Project::create(
            id: $id,
            name: $name,
            description: $description,
            clientId: $clientId,
            managerId: $managerId,
            technologies: $technologies,
            startDate: $startDate,
            endDate: $endDate,
        );

        $this->projectRepository->add($project);

        return $project;
    }
}
