<?php

namespace App\UserManagement\Application\GetUser;

use App\UserManagement\Domain\AppUser;
use App\UserManagement\Domain\AppUserRepositoryInterface;

class GetUserHandler
{
    public function __construct(
        private readonly AppUserRepositoryInterface $userRepository
    ) {}

    public function handle(GetUserQuery $query): AppUser
    {
        $user = $this->userRepository->findById($query->userId);

        if ($user === null) {
            throw new \DomainException('User not found.');
        }

        return $user;
    }

}
