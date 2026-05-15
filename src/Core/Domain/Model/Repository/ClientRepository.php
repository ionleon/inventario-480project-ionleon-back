<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Repository;

use App\Core\Domain\Exception\Client\ClientNotFoundException;
use App\Core\Domain\Model\Aggregate\Client;
use App\Core\Domain\Model\VO\Client\ClientId;
use App\Core\Domain\Model\VO\Client\ClientName;
use App\Shared\Domain\Pagination\PaginatedResult;

interface ClientRepository
{
    public function add(Client $client): void;

    public function remove(Client $client): void;

    public function find(ClientId $id): ?Client;

    /** @throws ClientNotFoundException */
    public function findOneOrFail(ClientId $id): Client;

    public function findOneByName(ClientName $name): ?Client;

    /** @return list<Client> */
    public function all(): array;

    public function findByFiltersPaginated(?string $term, ?bool $isActive, int $page, int $limit): PaginatedResult;
}
