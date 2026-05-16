<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Fixtures;

use App\Core\Domain\Model\Aggregate\Project;
use App\Core\Domain\Model\Aggregate\ProjectRole;
use App\Core\Domain\Model\Aggregate\ProjectUser;
use App\Core\Domain\Model\Aggregate\User;
use App\Core\Domain\Model\VO\Project\ProjectId;
use App\Core\Domain\Model\VO\ProjectRole\ProjectRoleId;
use App\Core\Domain\Model\VO\ProjectUser\ProjectUserAllocation;
use App\Core\Domain\Model\VO\ProjectUser\ProjectUserId;
use App\Core\Domain\Model\VO\User\UserId;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Uid\Uuid;

final class ProjectUserFixtures extends Fixture implements DependentFixtureInterface
{
    public const PROJECT_USER_DEV_REFERENCE = 'p-user-dev-';

    public function load(ObjectManager $manager): void
    {
        /** @var User $adminUser */
        $adminUser = $this->getReference(UserFixtures::ADMIN_REFERENCE, User::class);
        /** @var User $devUser */
        $devUser = $this->getReference(UserFixtures::DEV_REFERENCE, User::class);

        /** @var ProjectRole $roleManager */
        $roleManager = $this->getReference('role-project-manager', ProjectRole::class);
        /** @var ProjectRole $roleDev */
        $roleDev = $this->getReference('role-developer', ProjectRole::class);

        for ($i = 0; $i < 5; $i++) {
            /** @var Project $project */
            $project = $this->getReference(ProjectFixtures::PROJECT_REF . $i, Project::class);

            $projectUserAdmin = ProjectUser::assign(
                id: new ProjectUserId(Uuid::v7()->toRfc4122()),
                projectId: new ProjectId((string) $project->id()),
                userId: new UserId((string) $adminUser->id()),
                roleId: new ProjectRoleId((string) $roleManager->id()),
                allocation: new ProjectUserAllocation(100),
            );

            $manager->persist($projectUserAdmin);

            if ($i < 3) {
                $projectUserDev = ProjectUser::assign(
                    id: new ProjectUserId(Uuid::v7()->toRfc4122()),
                    projectId: new ProjectId((string) $project->id()),
                    userId: new UserId((string) $devUser->id()),
                    roleId: new ProjectRoleId((string) $roleDev->id()),
                    allocation: new ProjectUserAllocation(100),
                );

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
            ProjectRoleFixtures::class,
        ];
    }
}
