<?php

namespace App\Shared\Infrastructure\Fixtures;

use App\ProjectManagement\Domain\Developments\Technology\Technology;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Uid\Uuid;

class TechnologyFixtures extends Fixture
{

    public const TECH_REF = 'tech-';
    private const TECHNOLOGIES = ['PHP', 'Symfony', 'React', 'Docker', 'MySQL'];

    public function load(ObjectManager $manager): void
    {
        foreach (self::TECHNOLOGIES as $key => $name) {
            $tech = new Technology();
            $tech->setId(Uuid::v7());
            $tech->setName($name);
            $manager->persist($tech);

            $this->addReference(self::TECH_REF . $key, $tech);
        }
        $manager->flush();
    }
}
