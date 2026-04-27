<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Project;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Uid\Uuid;


class ProjectFixtures extends Fixture implements DependentFixtureInterface
{

    public const PROJECT_REF = 'project-';
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 5; $i++) {
            $project = new Project();
            $project->setId(Uuid::v7());
            $project->setName($faker->sentence(3));
            $project->setDescription($faker->paragraph());
            $project->setStartedAt($faker->dateTimeBetween('-1 year', 'now'));
            $project->setIsActive($faker->boolean(80));

            $client = $this->getReference(ClientFixtures::CLIENT_REF . rand(0, 4), Client::class);
            $project->setClient($client);

            $manager->persist($project);
            $this->addReference(self::PROJECT_REF . $i, $project);
        }

        $manager->flush();

    }

    public function getDependencies(): array
    {
        return [
            ClientFixtures::class
        ];
    }
}
