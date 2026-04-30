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

    public function changePassword(AppUser $user, string $oldPassword, string $newPassword):void
    {
        if (!$this->passwordHasher->isPasswordValid($user, $oldPassword)) {
            throw new \InvalidArgumentException('La constraseña actual no es correct.');
        }

        $this->resetPassword($user, $newPassword);
    }

    public function resetPassword(AppUser $user, string $newPassword): void
    {
        if (strlen($newPassword) < 8) {
            throw new \InvalidArgumentException('New password must be at lest 8 characters long.');
        }

        $hashedPassword = $this->passwordHasher->hashPassword($user, $newPassword);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
