<?php

namespace App\DataFixtures;

use App\Entity\AppUser;
use App\Enum\SystemRole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

class UserFixtures extends Fixture
{

    public const ADMIN_REFERENCE = 'user-admin';
    public const DEV_REFERENCE = 'user-dev';
    public const INACTIVE_REFERENCE = 'user-inactive';

    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}
    public function load(ObjectManager $manager): void
    {
        $usersData = [
            [
                'ref' => self::ADMIN_REFERENCE,
                'id' => '018e6b10-7b2a-7111-a321-446655440100',
                'email' => 'admin@example.com',
                'name' => 'Admin',
                'surname' => 'System',
                'role' => SystemRole::ADMIN,
                'active' => true
            ],
            [
                'ref' => self::DEV_REFERENCE,
                'id' => '018e6b10-7b2a-7111-a321-446655440101',
                'email' => 'dev@example.com',
                'name' => 'John',
                'surname' => 'Doe',
                'role' => SystemRole::EMPLOYEE,
                'active' => true
            ],
            [
                'ref' => self::INACTIVE_REFERENCE,
                'id' => '018e6b10-7b2a-7111-a321-446655440102',
                'email' => 'inactive@example.com',
                'name' => 'Jane',
                'surname' => 'Smith',
                'role' => SystemRole::EMPLOYEE,
                'active' => false
            ],
        ];

        foreach ($usersData as $data) {
            $user = new AppUser();
            $user->setId(Uuid::fromString($data['id']));
            $user->setEmail($data['email']);
            $user->setName($data['name']);
            $user->setSurname($data['surname']);
            $user->setFirstTime(false);
            $user->setIsActive($data['active']);
            $user->setRole($data['role']);

            $hashedPassword = $this->passwordHasher->hashPassword($user, 'password1234');
            $user->setPassword($hashedPassword);

            $manager->persist($user);

            // Guardamos la referencia usando la clave 'ref' del array
            $this->addReference($data['ref'], $user);
        }
        $manager->flush();
    }
}
