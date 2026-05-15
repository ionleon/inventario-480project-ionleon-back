<?php

declare(strict_types=1);

namespace App\Core\Application\Query\User\ListUser;

use App\App\UI\API\Controller\User\ListUser\ListUserResponse;
use App\Core\Application\Bus\QueryHandler;
use App\Core\Application\DTO\UserFilters;
use App\Core\Domain\Model\Repository\UserRepository;

final readonly class ListUserHandler implements QueryHandler
{
    public function __construct(private UserRepository $repository) {}

    public function __invoke(ListUserQuery $query): ListUserResponse
    {
        $filters = new UserFilters(
            term: $query->term,
            role: $query->role,
            isActive: $query->isActive,
        );

        $result = $this->repository->findByFiltersPaginated($filters, $query->page, $query->limit);

        return ListUserResponse::from($result);
    }
}
