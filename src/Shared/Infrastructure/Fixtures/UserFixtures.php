<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Fixtures;

use App\Core\Domain\Model\Aggregate\User;
use App\Core\Domain\Model\VO\Common\Email;
use App\Core\Domain\Model\VO\Common\Password;
use App\Core\Domain\Model\VO\User\UserId;
use App\Core\Domain\Model\VO\User\UserName;
use App\Core\Domain\Model\VO\User\UserSurname;
use App\Shared\Domain\Enum\SystemRole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserFixtures extends Fixture
{
    public const ADMIN_REFERENCE = 'user-admin';
    public const DEV_REFERENCE = 'user-dev';
    public const INACTIVE_REFERENCE = 'user-inactive';

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $usersData = [
            [
                'ref'     => self::ADMIN_REFERENCE,
                'id'      => '018e6b10-7b2a-7111-a321-446655440100',
                'email'   => 'admin@example.com',
                'name'    => 'Admin',
                'surname' => 'System',
                'role'    => SystemRole::ADMIN,
                'active'  => true,
            ],
            [
                'ref'     => self::DEV_REFERENCE,
                'id'      => '018e6b10-7b2a-7111-a321-446655440101',
                'email'   => 'dev@example.com',
                'name'    => 'John',
                'surname' => 'Doe',
                'role'    => SystemRole::EMPLOYEE,
                'active'  => true,
            ],
            [
                'ref'     => self::INACTIVE_REFERENCE,
                'id'      => '018e6b10-7b2a-7111-a321-446655440102',
                'email'   => 'inactive@example.com',
                'name'    => 'Jane',
                'surname' => 'Smith',
                'role'    => SystemRole::EMPLOYEE,
                'active'  => false,
            ],
        ];

        foreach ($usersData as $data) {
            // Create with placeholder so we have a UserInterface for the hasher.
            $user = User::create(
                id: new UserId($data['id']),
                email: new Email($data['email']),
                name: new UserName($data['name']),
                surname: new UserSurname($data['surname']),
                password: new Password('placeholder'),
                role: $data['role'],
            );

            // Hash using the real user instance (hashPassword only needs UserInterface).
            $hashed = $this->passwordHasher->hashPassword($user, 'password1234');
            $user->resetPasswordByAdmin(new Password($hashed));

            if (!$data['active']) {
                $user->deactivate();
            }

            $user->markAsReturning();

            $manager->persist($user);
            $this->addReference($data['ref'], $user);
        }

        $manager->flush();
    }
}
