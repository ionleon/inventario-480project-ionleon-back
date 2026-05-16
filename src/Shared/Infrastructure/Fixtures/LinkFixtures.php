<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Fixtures;

use App\Core\Domain\Model\Aggregate\Link;
use App\Core\Domain\Model\Aggregate\Project;
use App\Core\Domain\Model\VO\Link\LinkId;
use App\Core\Domain\Model\VO\Link\LinkUrl;
use App\Core\Domain\Model\VO\Project\ProjectId;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Uid\Uuid;

final class LinkFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 5; $i++) {
            /** @var Project $project */
            $project = $this->getReference(ProjectFixtures::PROJECT_REF . $i, Project::class);

            $numLinks = rand(1, 3);
            for ($j = 0; $j < $numLinks; $j++) {
                $link = Link::create(
                    id: new LinkId(Uuid::v7()->toRfc4122()),
                    projectId: new ProjectId((string) $project->id()),
                    url: new LinkUrl($faker->url()),
                );

                $manager->persist($link);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [ProjectFixtures::class];
    }
}
