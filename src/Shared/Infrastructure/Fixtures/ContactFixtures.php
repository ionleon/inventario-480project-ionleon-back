<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Fixtures;

use App\Core\Domain\Model\Aggregate\Client;
use App\Core\Domain\Model\Aggregate\Contact;
use App\Core\Domain\Model\VO\Client\ClientId;
use App\Core\Domain\Model\VO\Common\Email;
use App\Core\Domain\Model\VO\Common\Phone;
use App\Core\Domain\Model\VO\Contact\ContactId;
use App\Core\Domain\Model\VO\Contact\ContactName;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Uid\Uuid;

final class ContactFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('es_ES');

        for ($i = 0; $i < 5; $i++) {
            /** @var Client $client */
            $client = $this->getReference(ClientFixtures::CLIENT_REF . $i, Client::class);
            $numContacts = rand(1, 3);

            for ($j = 0; $j < $numContacts; $j++) {
                $contact = Contact::create(
                    id: new ContactId(Uuid::v7()->toRfc4122()),
                    clientId: new ClientId((string) $client->id()),
                    fullName: new ContactName($faker->name()),
                    email: new Email($faker->safeEmail()),
                    phoneNumber: new Phone($faker->phoneNumber()),
                    note: $faker->boolean(50) ? $faker->sentence() : null,
                    isMain: $j === 0,
                );

                $manager->persist($contact);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [ClientFixtures::class];
    }
}
