<?php

namespace App\UserManagement\Application\DeleteUser;

use App\Auth\Application\AuthService;
use App\UserManagement\Domain\AppUser;
use App\UserManagement\Domain\AppUserRepositoryInterface;

class DeleteUserHandler
{

    public function __construct(
        private readonly AppUserRepositoryInterface $userRepository,
        private readonly AuthService                $authService,
    ) {}

    public function handle(DeleteUserCommand $command): void
    {
        $user = $this->userRepository->findById($command->userId);

        if ($user === null) {
            throw new \DomainException('User not found');
        }

        $this->authService->forceLogout($user);
        $this->userRepository->delete($user);
    }

}
