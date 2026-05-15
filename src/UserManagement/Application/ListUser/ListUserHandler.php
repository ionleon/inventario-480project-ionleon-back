<?php

namespace App\UserManagement\Application\ListUser;

use App\Shared\Domain\Pagination\PaginatedResult;
use App\UserManagement\Domain\AppUserRepositoryInterface;
use App\UserManagement\Domain\UserFilters;

class ListUserHandler
{
    public function __construct(
        private readonly AppUserRepositoryInterface $userRepository,
    ) {}

    public function handle(ListUserQuery $query): PaginatedResult
    {
        $filters = new UserFilters(
            term:     $query->term,
            role:     $query->role,
            isActive: $query->isActive,
        );

        return $this->userRepository->findByFiltersPaginated($filters, $query->page, $query->limit);

    }
}
