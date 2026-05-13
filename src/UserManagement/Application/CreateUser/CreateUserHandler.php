<?php

namespace App\UserManagement\Application\CreateUser;

use App\UserManagement\Domain\AppUser;
use App\UserManagement\Domain\AppUserRepositoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

class CreateUserHandler
{
    public function __construct(
      private readonly AppUserRepositoryInterface  $userRepository,
      private readonly UserPasswordHasherInterface $passwordHasher,
    ) {}

    public function handle(CreateUserCommand $command): AppUser
    {
        if (!isset($command->email, $command->password)) {
            throw new \InvalidArgumentException('Email and password are required.');
        }

        $existing = $this->userRepository->findByEmail($command->email);
        if ($existing !== null) {
            throw new \DomainException('Email already exists.');
        }

        $user = new AppUser();
        $user->setId(Uuid::fromString($command->id));
        $user->setEmail($command->email);
        $user->setName($command->name);
        $user->setSurname($command->surname);
        $user->setRole($command->role);
        $user->setFirstTime(true);
        $user->setIsActive(true);
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, $command->password)
        );

        $this->userRepository->save($user);

        return $user;
    }
}
