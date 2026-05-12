<?php

namespace App\UserManagement\Application;

use App\Auth\Application\AuthService;
use App\UserManagement\Domain\AppUser;
use App\UserManagement\Domain\AppUserRepositoryInterface;
use App\UserManagement\Infrastructure\DoctrineUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

class UserService
{
    public function __construct(
        private readonly AppUserRepositoryInterface      $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly AuthService                 $authService
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

        return $this->update($user, $data);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function update(AppUser $user, array $data): AppUser
    {
        if (isset($data['name'])) $user->setName($data['name']);
        if (isset($data['surname'])) $user->setSurname($data['surname']);
        if (isset($data['role'])) $user->setRole($data['role']);

        if (isset($data['is_active'])) {
            $user->setIsActive($data['is_active']);
        }

        $this->userRepository->save($user);

        $this->authService->forceLogout($user);

        return $user;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function changePassword(AppUser $user, string $oldPassword, string $newPassword):void
    {
        if (!$this->passwordHasher->isPasswordValid($user, $oldPassword)) {
            throw new \InvalidArgumentException('Actual password is not correct.');
        }

        $this->resetPassword($user, $newPassword);
    }

    /**
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function resetPassword(AppUser $user, string $newPassword): void
    {
        if (strlen($newPassword) < 8) {
            throw new \InvalidArgumentException('New password must be at lest 8 characters long.');
        }

        $hashedPassword = $this->passwordHasher->hashPassword($user, $newPassword);
        $user->setPassword($hashedPassword);


        $this->userRepository->save($user);
        $this->authService->forceLogout($user);
    }

    public function delete(AppUser $user) : void
    {
        $this->userRepository->delete($user);
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function toggleActivation(AppUser $user): void
    {
        $this->userRepository->updateUserActivationWithRelation($user, !$user->isActive());
        $this->userRepository->save($user);

        if ($user->isActive() === false) {
            $this->authService->forceLogout($user);
        }
    }

}
