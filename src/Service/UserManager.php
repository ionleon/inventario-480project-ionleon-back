<?php

namespace App\Service;

use App\Entity\AppUser;
use App\Repository\AppUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

class UserManager
{
    public function __construct(
        private readonly AppUserRepository $userRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher
    )
    {}

    public function create(array $data): AppUser
    {

        if (!isset($data['email'], $data['password'])) {
            throw new \InvalidArgumentException('Email and password are required.');
        }

        $user = new AppUser();

        $user->setId(Uuid::fromString($data['id']));

        $hashed = $this->passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashed);

        $user->setFirstTime(true);
        $user->setIsActive(true);

        return $this->save($user, $data);
    }

    public function save(AppUser $user, array $data): AppUser
    {
        if (isset($data['name'])) {
            $user->setName($data['name']);
        }

        if (isset($data['surname'])) {
            $user->setSurname($data['surname']);
        }

        if (isset($data['role'])) {
            $user->setRole($data['role']);
        }

        if (isset($data['is_active'])) {
            $user->setIsActive($data['is_active']);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
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
        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function remove(AppUser $user) : void
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();

    }

    /**
     * @throws Exception
     */
    public function deactivateUser(AppUser $user): void
    {
        $this->userRepository->deactivateUserWithRelation($user);
        $this->entityManager->flush();
    }

}
