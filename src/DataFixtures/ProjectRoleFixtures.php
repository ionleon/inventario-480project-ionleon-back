<?php

namespace App\DataFixtures;

use App\Entity\ProjectRole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProjectRoleFixtures extends Fixture
{

    public const ADMIN_ROLE_REFERENCE = 'role-admin';
    public const DEV_ROLE_REFERENCE = 'role-dev';

    public function load(ObjectManager $manager): void
    {
        $roles = [
            ['name' => 'Project Manager'],
            ['name' => 'Tech Lead'],
            ['name' => 'Backend Developer'],
            ['name' => 'Frontend Developer'],
            ['name' => 'UI/UX Designer'],
            ['name' => 'QA Engineer'],
        ];

        foreach ($roles as $data) {
            $role = new ProjectRole();
            $role->setName($data['name']);

            $manager->persist($role);

            $this->addReference('role-' . strtolower(str_replace(' ', '-', $data['name'])), $role);
        }

        $manager->flush();
    }
}
