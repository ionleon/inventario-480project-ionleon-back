<?php

namespace App\UserManagement\Application\ResetPassword;

use App\Auth\Application\AuthService;
use App\UserManagement\Domain\AppUserRepositoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ResetPasswordHandler
{
    public function __construct(
        private readonly AppUserRepositoryInterface  $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly AuthService                $authService,
    ) {}

    public function handle(ResetPasswordCommand $command): void
    {
        $user = $this->userRepository->findById($command->userId);

        if (!$user) {
            throw new \DomainException('User not found');
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
