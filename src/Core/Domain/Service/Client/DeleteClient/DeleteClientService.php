<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\Client\DeleteClient;

use App\Core\Domain\Exception\Client\ClientNotFoundException;
use App\Core\Domain\Model\Repository\ClientRepository;
use App\Core\Domain\Model\VO\Client\ClientId;

final readonly class DeleteClientService implements DeleteClientServiceInterface
{
    public function __construct(private ClientRepository $repository) {}

    /**
     * @throws ClientNotFoundException
     * Cross-aggregate 'has contacts/projects' check deferred to Plan 4/Plan 5 if needed.
     */
    public function __invoke(ClientId $id): void
    {
        $client = $this->repository->findOneOrFail($id);
        $this->repository->remove($client);
    }
}
