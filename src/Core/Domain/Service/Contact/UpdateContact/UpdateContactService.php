<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\Contact\UpdateContact;

use App\Core\Domain\Exception\Contact\ContactNotFoundException;
use App\Core\Domain\Model\Aggregate\Contact;
use App\Core\Domain\Model\Repository\ContactRepository;
use App\Core\Domain\Model\VO\Common\Email;
use App\Core\Domain\Model\VO\Common\Phone;
use App\Core\Domain\Model\VO\Contact\ContactId;
use App\Core\Domain\Model\VO\Contact\ContactName;

final readonly class UpdateContactService implements UpdateContactServiceInterface
{
    public function __construct(private ContactRepository $repository)
    {
    }

    /**
     * @throws ContactNotFoundException
     */
    public function __invoke(
        ContactId $id,
        ContactName $fullName,
        Email $email,
        Phone $phoneNumber,
        ?string $note,
    ): Contact {
        $contact = $this->repository->findOneOrFail($id);
        $contact->update($fullName, $email, $phoneNumber, $note);

        return $contact;
    }
}
