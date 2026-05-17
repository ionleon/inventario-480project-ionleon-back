<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\TimeEntry\UpdateTimeEntry;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class UpdateTimeEntryRequest
{
    public function __construct(
        #[Assert\NotBlank]
        public string $date = '',
        #[Assert\NotBlank]
        #[Assert\Positive]
        public float $hours = 0.0,
        public ?string $description = null,
    ) {
    }
}
