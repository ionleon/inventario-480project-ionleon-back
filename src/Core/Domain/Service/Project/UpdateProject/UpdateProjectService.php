<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\Project\UpdateProject;

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

final readonly class UpdateProjectService implements UpdateProjectServiceInterface
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
        bool $isActive,
    ): Project {
        $project = $this->projectRepository->findOneOrFail($id);

        $existing = $this->projectRepository->findOneByName($name);
        if ($existing !== null && !$existing->id()->equals($id)) {
            throw new DuplicatedProjectNameException((string) $name);
        }

        $this->clientRepository->findOneOrFail($clientId);
        $this->userRepository->findOneOrFail($managerId);

        $technologies = array_map(
            fn(TechnologyId $tid) => $this->technologyRepository->findOneOrFail($tid),
            $technologyIds,
        );

        $project->update(
            name: $name,
            description: $description,
            clientId: $clientId,
            managerId: $managerId,
            technologies: $technologies,
            startDate: $startDate,
            endDate: $endDate,
            isActive: $isActive,
        );

        return $project;
    }
}
