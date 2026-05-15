<?php

namespace App\Shared\Infrastructure\Fixtures;

use App\ClientManagement\Domain\Client\Client;
use App\ClientManagement\Domain\Sector\Sector;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Uid\Uuid;


class ClientFixtures extends Fixture implements DependentFixtureInterface
{

    public const CLIENT_REF = 'client_';

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('es_ES');

        for ($i = 0; $i < 5; $i++) {
            $client = new Client();
            $client->setId(Uuid::v7());
            $client->setName($faker->company());
            $client->setIsActive(true);

            $client->setSector($this->getReference(SectorFixtures::SECTOR_REF . rand(0, 3), Sector::class));

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
