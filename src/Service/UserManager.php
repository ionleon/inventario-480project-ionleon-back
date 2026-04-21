<?php

namespace App\Service;

use App\Entity\AppUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserManager
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher
    )
    {}

    public function create(AppUser $user): AppUser
    {
        $plainPassword = $user->getPassword();

        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);

        $user->setFirstTime(true);
        $user->setIsActive(true);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function update(AppUser $user): void
    {
        $this->entityManager->flush();
    }

    public function remove(AppUser $user) : void
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();

    }

    public function deactivate(AppUser $user): void
    {
        $user->setIsActive(false);
        $this->entityManager->flush();
    }
}
