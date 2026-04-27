<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Project;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;


class ProjectFixtures extends Fixture
{

    public const PROJECT_REF = 'project-';
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $client = new Client();
        $client->setName($faker->company());
        $manager->persist($client);

        for ($i = 0; $i < 5; $i++) {
            $project = new Project();
            $project->setName($faker->sentence(3));
            $project->setDescription($faker->paragraph());
            $project->setStartedAt($faker->dateTimeBetween('-1 year', 'now'));
            $project->setIsActive($faker->boolean(80));
            $project->setClient($client);

            $manager->persist($project);
            $this->addReference(self::PROJECT_REF . $i, $project);
        }

        $manager->flush();

    }
}
