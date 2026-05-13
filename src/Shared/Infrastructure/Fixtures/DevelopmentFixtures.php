<?php

namespace App\Shared\Infrastructure\Fixtures;

use App\ProjectManagement\Domain\Developments\Development;
use App\ProjectManagement\Domain\Developments\Technology\Technology;
use App\ProjectManagement\Domain\Project\Project;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Uid\Uuid;

class DevelopmentFixtures extends Fixture implements DependentFixtureInterface
{

    public const DEVELOPMENT_REFERENCE = 'dev-';

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('es_ES');

        for ($i = 0; $i < 10; $i++) {
            $development = new Development();

            $development->setId(Uuid::v7());

            // Asignamos relaciones (usando referencias de otras fixtures)
            $development->setProject($this->getReference(ProjectFixtures::PROJECT_REF . $faker->numberBetween(1, 4), Project::class));
            $development->setTechnology($this->getReference(TechnologyFixtures::TECH_REF . $faker->numberBetween(1, 4), Technology::class));

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
