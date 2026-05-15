<?php

declare(strict_types=1);

namespace App\Core\Application\Query\Project\ListProjects;

use App\App\UI\API\Controller\Project\ListProjects\ListProjectsResponse;
use App\Core\Application\Bus\QueryHandler;
use App\Core\Domain\Model\Repository\ProjectRepository;

final readonly class ListProjectsHandler implements QueryHandler
{
    public function __construct(private ProjectRepository $repository) {}

    public function __invoke(ListProjectsQuery $query): ListProjectsResponse
    {
        $result = $this->repository->findByFiltersPaginated(
            term: $query->term,
            clientId: $query->clientId,
            isActive: $query->isActive,
            page: $query->page,
            limit: $query->limit,
        );

        return ListProjectsResponse::from($result);
    }
}
