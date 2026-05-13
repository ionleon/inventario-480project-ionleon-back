<?php

namespace App\UserManagement\Application\ListUser;

use App\UserManagement\Domain\AppUserRepositoryInterface;
use App\UserManagement\Domain\UserFilters;

class ListUserHandler
{
    public function __construct(
        private readonly AppUserRepositoryInterface $userRepository,
    ) {}

    public function handle(ListUserQuery $query): array
    {
        $filters = new UserFilters(
            term:     $query->term,
            role:     $query->role,
            isActive: $query->isActive,
        );

        return $this->userRepository->findByFiltersPaginated($filters, $query->page, $query->limit);

    }
}
