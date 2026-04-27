<?php

namespace App\DataFixtures;

use App\Entity\Sector;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SectorFixtures extends Fixture
{
    public const SECTOR_REF = 'sector-';

    public function load(ObjectManager $manager): void
    {
        $sectores = ['Tecnología', 'Salud', 'Finanzas', 'Educación'];

        foreach ($sectores as $key => $nombre) {
            $sector = new Sector();
            $sector->setName($nombre);
            $manager->persist($sector);
            $this->addReference(self::SECTOR_REF . $key, $sector);
        }
        $manager->flush();
    }
}
