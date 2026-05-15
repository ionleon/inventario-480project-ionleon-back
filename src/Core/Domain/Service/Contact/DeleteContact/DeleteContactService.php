<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\Contact\DeleteContact;

use App\Core\Domain\Exception\Contact\ContactNotFoundException;
use App\Core\Domain\Model\Repository\ContactRepository;
use App\Core\Domain\Model\VO\Contact\ContactId;

final readonly class DeleteContactService implements DeleteContactServiceInterface
{
    public function __construct(private ContactRepository $repository) {}

    /**
     * @throws ContactNotFoundException
     */
    public function __invoke(ContactId $id): void
    {
        $contact = $this->repository->findOneOrFail($id);
        $contact->delete();
        $this->repository->remove($contact);
    }
}
