<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\Client\DeleteClient;

use App\Core\Domain\Exception\Client\ClientNotFoundException;
use App\Core\Domain\Model\VO\Client\ClientId;

interface DeleteClientServiceInterface
{
    /**
     * @throws ClientNotFoundException
     */
    public function __invoke(ClientId $id): void;
}
