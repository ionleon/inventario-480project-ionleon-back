<?php

declare(strict_types=1);

namespace App\Core\Application\Query\Client\GetClient;

use App\App\UI\API\Controller\Client\GetClient\GetClientResponse;
use App\Core\Application\Bus\QueryHandler;
use App\Core\Domain\Model\Repository\ClientRepository;
use App\Core\Domain\Model\VO\Client\ClientId;

final readonly class GetClientHandler implements QueryHandler
{
    public function __construct(private ClientRepository $repository)
    {
    }

    public function __invoke(GetClientQuery $query): GetClientResponse
    {
        $client = $this->repository->findOneOrFail(new ClientId($query->id));

        return GetClientResponse::from($client);
    }
}
