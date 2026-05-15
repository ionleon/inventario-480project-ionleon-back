<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Repository;

use App\Core\Domain\Model\DTO\UserFilters;
use App\Core\Domain\Exception\User\UserNotFoundException;
use App\Core\Domain\Model\Aggregate\User;
use App\Core\Domain\Model\VO\Common\Email;
use App\Core\Domain\Model\VO\User\UserId;
use App\Shared\Domain\Pagination\PaginatedResult;

interface UserRepository
{
    public function add(User $user): void;

    public function remove(User $user): void;

    public function find(UserId $id): ?User;

    /** @throws UserNotFoundException */
    public function findOneOrFail(UserId $id): User;

    public function findOneByEmail(Email $email): ?User;

    /** @return list<User> */
    public function all(): array;

    public function findByFiltersPaginated(UserFilters $filters, int $page, int $limit): PaginatedResult;
}
