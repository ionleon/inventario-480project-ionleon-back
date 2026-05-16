<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Fixtures;

use App\Core\Domain\Model\Aggregate\Client;
use App\Core\Domain\Model\Aggregate\Project;
use App\Core\Domain\Model\VO\Client\ClientId;
use App\Core\Domain\Model\VO\Project\ProjectDescription;
use App\Core\Domain\Model\VO\Project\ProjectId;
use App\Core\Domain\Model\VO\Project\ProjectName;
use App\Core\Domain\Model\VO\Project\ProjectStartDate;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Uid\Uuid;

final class ProjectFixtures extends Fixture implements DependentFixtureInterface
{
    public const PROJECT_REF = 'project-';

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 5; $i++) {
            /** @var Client $client */
            $client = $this->getReference(ClientFixtures::CLIENT_REF . rand(0, 4), Client::class);

            $startedAt = DateTimeImmutable::createFromMutable(
                $faker->dateTimeBetween('-1 year', 'now')
            );

            $project = Project::create(
                id: new ProjectId(Uuid::v7()->toRfc4122()),
                name: new ProjectName($faker->sentence(3)),
                description: new ProjectDescription($faker->paragraph()),
                clientId: new ClientId((string) $client->id()),
                managerId: null,
                technologies: [],
                startDate: new ProjectStartDate($startedAt),
            );

            $manager->persist($project);
            $this->addReference(self::PROJECT_REF . $i, $project);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [ClientFixtures::class];
    }
}
