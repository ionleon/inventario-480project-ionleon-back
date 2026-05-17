<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\Contact\CreateContact;

use App\Core\Domain\Exception\Client\ClientNotFoundException;
use App\Core\Domain\Model\Aggregate\Contact;
use App\Core\Domain\Model\Repository\ClientRepository;
use App\Core\Domain\Model\Repository\ContactRepository;
use App\Core\Domain\Model\VO\Client\ClientId;
use App\Core\Domain\Model\VO\Common\Email;
use App\Core\Domain\Model\VO\Common\Phone;
use App\Core\Domain\Model\VO\Contact\ContactId;
use App\Core\Domain\Model\VO\Contact\ContactName;

final readonly class CreateContactService implements CreateContactServiceInterface
{
    public function __construct(
        private ClientRepository $clientRepository,
        private ContactRepository $contactRepository,
    ) {
    }

    /**
     * @throws ClientNotFoundException
     */
    public function __invoke(
        ContactId $id,
        ClientId $clientId,
        ContactName $fullName,
        Email $email,
        Phone $phoneNumber,
        ?string $note,
    ): Contact {
        $this->clientRepository->findOneOrFail($clientId);

        $existingContacts = $this->contactRepository->findByClient($clientId);
        $isMain = count($existingContacts) === 0;

        $contact = Contact::create(
            id: $id,
            clientId: $clientId,
            fullName: $fullName,
            email: $email,
            phoneNumber: $phoneNumber,
            note: $note,
            isMain: $isMain,
        );

        $this->contactRepository->add($contact);

        return $contact;
    }
}
