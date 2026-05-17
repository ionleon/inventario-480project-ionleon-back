<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\TimeEntry\CreateTimeEntryForProject;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateTimeEntryForProjectRequest
{
    public function __construct(
        #[Assert\Uuid]
        public ?string $id = null,
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $userId = '',
        #[Assert\NotBlank]
        public string $date = '',
        #[Assert\NotBlank]
        #[Assert\Positive]
        public float $hours = 0.0,
        public ?string $description = null,
    ) {
    }
}
