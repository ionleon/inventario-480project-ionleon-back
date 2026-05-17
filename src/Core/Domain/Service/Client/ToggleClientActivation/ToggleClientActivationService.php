<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\Client\ToggleClientActivation;

use App\Core\Domain\Exception\Client\ClientNotFoundException;
use App\Core\Domain\Model\Repository\ClientRepository;
use App\Core\Domain\Model\VO\Client\ClientId;

final readonly class ToggleClientActivationService implements ToggleClientActivationServiceInterface
{
    public function __construct(private ClientRepository $repository)
    {
    }

    /** @throws ClientNotFoundException */
    public function __invoke(ClientId $id): void
    {
        $client = $this->repository->findOneOrFail($id);
        $client->toggleActivation();
    }
}
