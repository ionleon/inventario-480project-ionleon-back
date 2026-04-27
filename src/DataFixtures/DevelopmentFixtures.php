<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class DevelopmentFixtures extends Fixture implements DependentFixtureInterface
{

    public const DEVELOPMENT_REFERENCE = 'dev-';

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('es_ES');

        for ($i = 0; $i < 10; $i++) {
            $development = new Development();

            // Asignamos relaciones (usando referencias de otras fixtures)
            $development->setProject($this->getReference(ProjectFixtures::PROJECT_REFERENCE . $faker->numberBetween(1, 5), Project::class));
            $development->setTechnology($this->getReference(TechnologyFixtures::TECH_REFERENCE . $faker->numberBetween(1, 5), Technology::class));

            $development->setName($faker->words(3, true));
            $development->setDescription($faker->paragraphs(2, true));
            $development->setUrlRepository($faker->url());

            $manager->persist($development);

            // Guardamos referencia para los Links
            $this->addReference(self::DEVELOPMENT_REFERENCE . $i, $development);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ProjectFixtures::class,
            TechnologyFixtures::class,
        ];
    }
}
