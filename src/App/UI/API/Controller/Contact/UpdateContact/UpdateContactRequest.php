<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\Contact\UpdateContact;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class UpdateContactRequest
{
    public function __construct(
        #[Assert\NotBlank, Assert\Length(min: 2, max: 255)]
        public string $fullName,
        #[Assert\NotBlank, Assert\Email]
        public string $email,
        #[Assert\NotBlank, Assert\Length(max: 30)]
        public string $phoneNumber,
        #[Assert\Length(max: 2000)]
        public ?string $note = null,
    ) {}
}
