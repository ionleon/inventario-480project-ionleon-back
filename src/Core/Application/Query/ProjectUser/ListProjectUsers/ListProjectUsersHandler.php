<?php

declare(strict_types=1);

namespace App\Core\Application\Query\ProjectUser\ListProjectUsers;

use App\App\UI\API\Controller\ProjectUser\ListProjectUsers\ProjectUserResponse;
use App\Core\Application\Bus\QueryHandler;
use App\Core\Domain\Model\Aggregate\ProjectUser;
use App\Core\Domain\Model\Repository\ProjectUserRepository;
use App\Core\Domain\Model\VO\Project\ProjectId;

final readonly class ListProjectUsersHandler implements QueryHandler
{
    public function __construct(private ProjectUserRepository $repository) {}

    /** @return list<ProjectUserResponse> */
    public function __invoke(ListProjectUsersQuery $query): array
    {
        $projectUsers = $this->repository->findByProject(new ProjectId($query->projectId));

        return array_map(
            static fn(ProjectUser $pu) => ProjectUserResponse::from($pu),
            $projectUsers,
        );
    }
}
