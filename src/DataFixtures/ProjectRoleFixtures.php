<?php

namespace App\DataFixtures;

use App\Entity\ProjectRole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Uid\Uuid;

class ProjectRoleFixtures extends Fixture
{

    public const ADMIN_ROLE_REFERENCE = 'role-admin';
    public const DEV_ROLE_REFERENCE = 'role-dev';

    public function load(ObjectManager $manager): void
    {
        $roles = [
            ['id' => '019dd397-475d-74cc-8b26-65e2324090d9','name' => 'PROJECT_MANAGER'],
            ['id' => '019dd397-475d-7a48-b120-7b70b9bac7b5', 'name' => 'TECH_LEAD'],
            ['id' => '019dd397-475d-7522-b2e1-82ccff8d50c4', 'name' => 'KAM'],
            ['id' => '019dd397-475d-784f-acee-1fd7d93fbd27', 'name' => 'DEVELOPER'],
        ];

        foreach ($roles as $data) {
            $role = new ProjectRole();
            $role->setId(Uuid::fromString($data['id']));
            $role->setName($data['name']);

            $manager->persist($role);

            $this->addReference('role-' . strtolower(str_replace([' ', '_'], '-', $data['name'])), $role);
        }

        $manager->flush();
    }
}
