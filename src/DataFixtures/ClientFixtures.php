<?php

namespace App\DataFixtures;

use App\Entity\Client;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;


class ClientFixtures extends Fixture implements DependentFixtureInterface
{

    public const CLIENT_REF = 'client_';

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('es_ES');

        for ($i = 0; $i < 5; $i++) {
            $client = new Client();
            $client->setName($faker->company());
            $client->setIsActive(true);

            $manager->persist($client);
            $this->addReference(self::CLIENT_REF . $i, $client);
        }
        $manager->flush();

    }

    public function getDependencies(): array
    {
        return [SectorFixtures::class];
    }


}
