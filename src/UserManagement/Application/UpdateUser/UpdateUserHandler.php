<?php

namespace App\UserManagement\Application\UpdateUser;

use App\Auth\Application\ForceLogout\ForceLogoutCommand;
use App\Auth\Application\ForceLogout\ForceLogoutHandler;
use App\UserManagement\Domain\AppUser;
use App\UserManagement\Domain\AppUserRepositoryInterface;

final class UpdateUserHandler
{
    public function __construct(
      private readonly AppUserRepositoryInterface $userRepository,
        private readonly ForceLogoutHandler         $forceLogoutHandler,
    ) {}

    public function handle(UpdateUserCommand $command): AppUser
    {
        $user = $this->userRepository->findById($command->userId);

        if (!$user) {
            throw new \DomainException('User not found');
        }

        if ($command->name !== null || $command->surname !== null) {
            $user->updateProfile($command->name ?? $user->getName(), $command->surname ?? $user->getSurname());
        }

        if ($command->role !== null) {
            $user->changeRole($command->role);
        }

        if ($command->isActive !== null) {
            $command->isActive ? $user->activate() : $user->deactivate();
        }

        $this->userRepository->save($user);
        $this->forceLogoutHandler->handle(new ForceLogoutCommand($user->getUserIdentifier()));

        return $user;

    }
}
