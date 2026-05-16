<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Fixtures;

use App\Core\Domain\Model\Aggregate\Sector;
use App\Core\Domain\Model\VO\Sector\SectorId;
use App\Core\Domain\Model\VO\Sector\SectorName;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Uid\Uuid;

final class SectorFixtures extends Fixture
{
    public const SECTOR_REF = 'sector-';

    public function load(ObjectManager $manager): void
    {
        $sectors = ['Tecnología', 'Salud', 'Finanzas', 'Educación'];

        foreach ($sectors as $key => $name) {
            $sector = Sector::create(
                id: new SectorId(Uuid::v7()->toRfc4122()),
                name: new SectorName($name),
            );

            $manager->persist($sector);
            $this->addReference(self::SECTOR_REF . $key, $sector);
        }

        $manager->flush();
    }
}
