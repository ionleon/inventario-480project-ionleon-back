<?php

namespace App\UserManagement\Application\UpdateUser;

use App\Auth\Application\AuthService;
use App\UserManagement\Domain\AppUser;
use App\UserManagement\Domain\AppUserRepositoryInterface;

final class UpdateUserHandler
{
    public function __construct(
      private readonly AppUserRepositoryInterface $userRepository,
      private readonly AuthService                $authService,
    ) {}

    public function handle(UpdateUserCommand $command): AppUser
    {
        $user = $this->userRepository->findById($command->userId);

        if (!$user) {
            throw new \DomainException('User not found');
        }

        if ($command->name !== null)     $user->setName($command->name);
        if ($command->surname !== null)  $user->setSurname($command->surname);
        if ($command->email !== null)    $user->setEmail($command->email);
        if ($command->role !== null)     $user->setRole($command->role);
        if ($command->isActive !== null) $user->setIsActive($command->isActive);

        $this->userRepository->save($user);
        $this->authService->forceLogout($user);

        return $user;

    }
}
