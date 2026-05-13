<?php

namespace App\UserManagement\Application\ChangePassword;

use App\Auth\Application\AuthService;
use App\UserManagement\Domain\AppUserRepositoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ChangePasswordHandler
{

    public function __construct(
        private readonly AppUserRepositoryInterface $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly AuthService $authService
    ) {}

    public function handle(ChangePasswordCommand $command): void
    {
        $user = $this->userRepository->findById($command->userId);

        if (!$user) {
            throw new \DomainException('User not found');
        }

        if (!$this->passwordHasher->isPasswordValid($user, $command->oldPassword)) {
            throw new \InvalidArgumentException('Current password is not correct.');
        }

        if (strlen($command->newPassword) < 8) {
            throw new \InvalidArgumentException('New password must be at least 8 characters long.');
        }

        $user->setPassword(
            $this->passwordHasher->hashPassword($user, $command->newPassword)
        );

        $this->userRepository->save($user);
        $this->authService->forceLogout($user);
    }

}
