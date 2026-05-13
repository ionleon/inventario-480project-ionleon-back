<?php

namespace App\Shared\Infrastructure\Fixtures;

use App\ProjectManagement\Domain\ProjectUser\ProjectUser;
use App\TimeManagement\Domain\TimeEntry;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Uid\Uuid;

class TimeEntryFixtures extends Fixture implements DependentFixtureInterface
{


    public function load(ObjectManager $manager): void
    {
        /** @var ProjectUser $projectUser */
        $projectUser = $this->getReference(ProjectUserFixtures::PROJECT_USER_DEV_REFERENCE. '0', ProjectUser::class);

        $entries = [
            [
                'id' => '018e6b10-7b2a-7111-a321-446655440901',
                'date' => '2026-04-25',
                'hour' => '8.00',
                'comment' => 'Análisis de requerimientos y diseño de base de datos.'
            ],
            [
                'id' => '018e6b10-7b2a-7111-a321-446655440902',
                'date' => '2026-04-26',
                'hour' => '6.50',
                'comment' => 'Implementación de controladores base y managers.'
            ],
            [
                'id' => '018e6b10-7b2a-7111-a321-446655440903',
                'date' => '2026-04-27',
                'hour' => '4.25',
                'comment' => 'Corrección de errores en la validación de UUIDs.'
            ],
        ];

        foreach ($entries as $data) {
            $timeEntry = new TimeEntry();
            $timeEntry->setId(Uuid::fromString($data['id']));
            $timeEntry->setProjectUser($projectUser);
            $timeEntry->setDate(new \DateTime($data['date']));
            $timeEntry->setHour($data['hour']);
            $timeEntry->setComment($data['comment']);

            $manager->persist($timeEntry);
        }

        $manager->flush();

    }

    public function getDependencies(): array
    {
        return [
            ProjectUserFixtures::class,
        ];
    }

}
