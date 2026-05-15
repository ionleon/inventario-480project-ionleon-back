<?php

namespace App\UserManagement\Application\DeleteUser;

use App\Auth\Application\ForceLogout\ForceLogoutCommand;
use App\Auth\Application\ForceLogout\ForceLogoutHandler;
use App\UserManagement\Domain\AppUserRepositoryInterface;

class DeleteUserHandler
{

    public function __construct(
        private readonly AppUserRepositoryInterface $userRepository,
        private readonly ForceLogoutHandler           $forceLogoutHandler,
    ) {}

    public function handle(DeleteUserCommand $command): void
    {
        $user = $this->userRepository->findById($command->userId);

        if ($user === null) {
            throw new \DomainException('User not found');
        }

        $this->forceLogoutHandler->handle(new ForceLogoutCommand($user->getUserIdentifier()));
        $this->userRepository->delete($user);
    }

}
