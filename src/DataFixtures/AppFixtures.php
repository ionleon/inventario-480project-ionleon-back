<?php

namespace App\DataFixtures;

use App\Entity\AppUser;
use App\Enum\SystemRole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

class AppFixtures extends Fixture
{

    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}
    public function load(ObjectManager $manager): void
    {
        $user = new AppUser();
        $user->setId(Uuid::v7());
        $user->setEmail('admin@example.com');
        $user->setName('Admin');
        $user->setSurname('User');
        $user->setFirstTime(false);
        $user->setIsActive(true);
        $user->setRole(SystemRole::ADMIN);

        $hashedPassword = $this->passwordHasher->hashPassword($user, 'password1234');
        $user->setPassword($hashedPassword);

        $manager->persist($user);
        $manager->flush();
    }
}
