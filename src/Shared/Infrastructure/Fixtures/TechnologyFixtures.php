<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Fixtures;

use App\Core\Domain\Model\Aggregate\Technology;
use App\Core\Domain\Model\VO\Technology\TechnologyId;
use App\Core\Domain\Model\VO\Technology\TechnologyName;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Uid\Uuid;

final class TechnologyFixtures extends Fixture
{
    public const TECH_REF = 'tech-';

    private const TECHNOLOGIES = ['PHP', 'Symfony', 'React', 'Docker', 'MySQL'];

    public function load(ObjectManager $manager): void
    {
        foreach (self::TECHNOLOGIES as $key => $name) {
            $tech = Technology::create(
                id: new TechnologyId(Uuid::v7()->toRfc4122()),
                name: new TechnologyName($name),
            );

            $manager->persist($tech);
            $this->addReference(self::TECH_REF . $key, $tech);
        }

        $manager->flush();
    }
}
