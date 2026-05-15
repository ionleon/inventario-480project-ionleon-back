<?php

declare(strict_types=1);

namespace App\Core\Application\Query\Technology\ListTechnologies;

use App\App\UI\API\Controller\Technology\ListTechnologies\ListTechnologiesResponse;
use App\Core\Application\Bus\QueryHandler;
use App\Core\Domain\Model\Repository\TechnologyRepository;

final readonly class ListTechnologiesHandler implements QueryHandler
{
    public function __construct(private TechnologyRepository $repository) {}

    public function __invoke(ListTechnologiesQuery $query): ListTechnologiesResponse
    {
        return ListTechnologiesResponse::from($this->repository->all());
    }
}
