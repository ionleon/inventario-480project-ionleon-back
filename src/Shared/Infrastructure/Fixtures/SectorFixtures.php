<?php

namespace App\Shared\Infrastructure\Fixtures;

use App\ClientManagement\Domain\Sector\Sector;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Uid\Uuid;

class SectorFixtures extends Fixture
{
    public const SECTOR_REF = 'sector-';

    public function load(ObjectManager $manager): void
    {
        $sectores = ['Tecnología', 'Salud', 'Finanzas', 'Educación'];

        foreach ($sectores as $key => $nombre) {
            $sector = new Sector();
            $sector->setId(Uuid::v7());
            $sector->setName($nombre);
            $manager->persist($sector);
            $this->addReference(self::SECTOR_REF . $key, $sector);
        }
        $manager->flush();
    }
}
