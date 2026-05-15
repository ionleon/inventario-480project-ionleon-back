<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\Contact\ListContactsByClient;

use App\Core\Domain\Model\Aggregate\Contact;

final readonly class ListContactsByClientResponse
{
    /** @param list<ContactResponse> $items */
    public function __construct(
        public array $items,
    ) {}

    /** @param list<Contact> $contacts */
    public static function from(array $contacts): self
    {
        return new self(
            items: array_map(
                static fn(Contact $contact) => ContactResponse::from($contact),
                $contacts,
            ),
        );
    }
}
