<?php

namespace App\UserManagement\Domain;

use App\Shared\Domain\Pagination\PaginatedResult;

interface AppUserRepositoryInterface
{
    public function findByFilters(UserFilters $filters) : array;

    public function findByFiltersPaginated(UserFilters $filters,  int $page, int $limit) : PaginatedResult;

    public function findById(string $id): ?AppUser;

    public function deactivateUserWithRelation(AppUser $user) : void;


}
