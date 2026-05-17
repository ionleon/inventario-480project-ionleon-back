<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\Contact\MarkContactAsMain;

use App\Core\Domain\Exception\Contact\ContactNotFoundException;
use App\Core\Domain\Model\Repository\ContactRepository;
use App\Core\Domain\Model\VO\Contact\ContactId;

final readonly class MarkContactAsMainService implements MarkContactAsMainServiceInterface
{
    public function __construct(private ContactRepository $repository)
    {
    }

    /**
     * @throws ContactNotFoundException
     */
    public function __invoke(ContactId $id): void
    {
        $contact = $this->repository->findOneOrFail($id);

        if ($contact->isMain()) {
            return;
        }

        $currentMain = $this->repository->findMainByClient($contact->clientId());
        if ($currentMain !== null) {
            $currentMain->unmarkAsMain();
        }

        $contact->markAsMain();
    }
}
