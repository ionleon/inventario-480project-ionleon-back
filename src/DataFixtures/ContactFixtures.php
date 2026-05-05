<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Contact;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Uid\Uuid;

class ContactFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('es_ES');

        // Iteramos sobre los 5 clientes que creamos en ClientFixtures
        for ($i = 0; $i < 5; $i++) {
            /** @var Client $client */
            $client = $this->getReference(ClientFixtures::CLIENT_REF . $i, Client::class);

            // Creamos entre 1 y 3 contactos por cliente
            $numContacts = rand(1, 3);

            for ($j = 0; $j < $numContacts; $j++) {
                $contact = new Contact();
                $contact->setId(Uuid::v7());
                $contact->setFullName($faker->name());
                $contact->setPhoneNumber($faker->phoneNumber());
                $contact->setEmail($faker->safeEmail());


                // El primer contacto del bucle será el principal (isMain)
                $contact->setIsMain($j === 0);

                $contact->setNote($faker->boolean(50) ? $faker->sentence() : null);
                $contact->setClient($client);

                $manager->persist($contact);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ClientFixtures::class,
        ];
    }
}
