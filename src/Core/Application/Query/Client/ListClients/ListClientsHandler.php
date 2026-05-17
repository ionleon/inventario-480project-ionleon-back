<?php

declare(strict_types=1);

namespace App\Core\Application\Query\Client\ListClients;

use App\App\UI\API\Controller\Client\ListClients\ListClientsResponse;
use App\Core\Application\Bus\QueryHandler;
use App\Core\Domain\Model\Repository\ClientRepository;

final readonly class ListClientsHandler implements QueryHandler
{
    public function __construct(private ClientRepository $repository)
    {
    }

    public function __invoke(ListClientsQuery $query): ListClientsResponse
    {
        $result = $this->repository->findByFiltersPaginated(
            term: $query->term,
            isActive: $query->isActive,
            page: $query->page,
            limit: $query->limit,
        );

        return ListClientsResponse::from($result);
    }
}
