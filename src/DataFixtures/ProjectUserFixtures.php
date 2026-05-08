<?php

namespace App\DataFixtures;

use App\Entity\AppUser;
use App\Entity\ProjectRole;
use App\ProjectManagement\Domain\Project\Project;
use App\ProjectManagement\Domain\ProjectUser\ProjectUser;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Uid\Uuid;

class ProjectUserFixtures extends Fixture implements DependentFixtureInterface
{
    public const PROJECT_USER_DEV_REFERENCE = 'p-user-dev-';
    public function load(ObjectManager $manager): void
    {

        $adminUser = $this->getReference(UserFixtures::ADMIN_REFERENCE, AppUser::class);
        $devUser = $this->getReference(UserFixtures::DEV_REFERENCE, AppUser::class);

        $roleManager = $this->getReference('role-project-manager', ProjectRole::class);
        $roleDev = $this->getReference('role-developer', ProjectRole::class);

        for ($i = 0; $i < 5; $i++) {
            $project = $this->getReference(ProjectFixtures::PROJECT_REF . $i, Project::class);

            $projectUserAdmin = new ProjectUser();

            $projectUserAdmin->setId(Uuid::v7());
            $projectUserAdmin->setProject($project);
            $projectUserAdmin->setAppUser($adminUser);
            $projectUserAdmin->setIsActive(true);
            $projectUserAdmin->setProjectRole($roleManager);

            $manager->persist($projectUserAdmin);


            if ($i < 3) {
                $projectUserDev = new ProjectUser();
                $projectUserDev->setId(Uuid::v7());
                $projectUserDev->setProject($project);
                $projectUserDev->setAppUser($devUser);
                $projectUserDev->setIsActive(true);
                $projectUserDev->setProjectRole($roleDev);

                $manager->persist($projectUserDev);


                $this->addReference(self::PROJECT_USER_DEV_REFERENCE . $i, $projectUserDev);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ProjectFixtures::class,
            UserFixtures::class,
            ProjectRoleFixtures::class
        ];
    }
}
