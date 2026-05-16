<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Fixtures;

use App\Core\Domain\Model\Aggregate\Client;
use App\Core\Domain\Model\Aggregate\Sector;
use App\Core\Domain\Model\VO\Client\ClientId;
use App\Core\Domain\Model\VO\Client\ClientName;
use App\Core\Domain\Model\VO\Sector\SectorId;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Uid\Uuid;

final class ClientFixtures extends Fixture implements DependentFixtureInterface
{
    public const CLIENT_REF = 'client_';

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('es_ES');

        for ($i = 0; $i < 5; $i++) {
            /** @var Sector $sector */
            $sector = $this->getReference(SectorFixtures::SECTOR_REF . rand(0, 3), Sector::class);

            $client = Client::create(
                id: new ClientId(Uuid::v7()->toRfc4122()),
                name: new ClientName($faker->company()),
                sectorId: new SectorId((string) $sector->id()),
            );

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
