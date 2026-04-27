<?php

namespace App\DataFixtures;

use App\Entity\Development;
use App\Entity\Link;
use App\Enum\Enviroment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class LinkFixtures extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $enviroments = Enviroment::cases();

        // Creamos entre 1 y 3 links para cada Development creado anteriormente
        for ($i = 0; $i < 10; $i++) {
            $dev = $this->getReference(DevelopmentFixtures::DEVELOPMENT_REFERENCE . $i, Development::class);

            $numLinks = rand(1, 3);
            for ($j = 0; $j < $numLinks; $j++) {
                $link = new Link();
                $link->setUrl($faker->url());
                $link->setEnviroment($faker->randomElement($enviroments));
                $link->setDevelopment($dev);

                $manager->persist($link);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            DevelopmentFixtures::class,
        ];
    }

}
