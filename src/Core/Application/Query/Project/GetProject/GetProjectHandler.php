<?php

declare(strict_types=1);

namespace App\Core\Application\Query\Project\GetProject;

use App\App\UI\API\Controller\Project\GetProject\GetProjectResponse;
use App\Core\Application\Bus\QueryHandler;
use App\Core\Domain\Model\Repository\ProjectRepository;
use App\Core\Domain\Model\VO\Project\ProjectId;

final readonly class GetProjectHandler implements QueryHandler
{
    public function __construct(private ProjectRepository $repository) {}

    public function __invoke(GetProjectQuery $query): GetProjectResponse
    {
        $project = $this->repository->findOneOrFail(new ProjectId($query->id));

        return GetProjectResponse::from($project);
    }
}
