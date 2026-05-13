<?php

namespace App\UserManagement\Domain;

use App\Shared\Domain\Pagination\PaginatedResult;

interface AppUserRepositoryInterface
{
    public function findByFilters(UserFilters $filters) : array;

    public function findByFiltersPaginated(UserFilters $filters,  int $page, int $limit) : PaginatedResult;

    public function findById(string $id): ?AppUser;

    public function findByEmail(string $email): ?AppUser;

    public function save(AppUser $user): void;

    public function delete(AppUser $user): void;

    public function updateUserActivationWithRelation(AppUser $user, bool $isActive) : void;


}
