<?php

declare(strict_types=1);

namespace App\Core\Application\Query\Contact\ListContactsByClient;

use App\App\UI\API\Controller\Contact\ListContactsByClient\ListContactsByClientResponse;
use App\Core\Application\Bus\QueryHandler;
use App\Core\Domain\Model\Repository\ClientRepository;
use App\Core\Domain\Model\Repository\ContactRepository;
use App\Core\Domain\Model\VO\Client\ClientId;

final readonly class ListContactsByClientHandler implements QueryHandler
{
    public function __construct(
        private ClientRepository $clientRepository,
        private ContactRepository $contactRepository,
    ) {}

    public function __invoke(ListContactsByClientQuery $query): ListContactsByClientResponse
    {
        $clientId = new ClientId($query->clientId);
        $this->clientRepository->findOneOrFail($clientId);

        $contacts = $this->contactRepository->findByClient($clientId);

        return ListContactsByClientResponse::from($contacts);
    }
}
