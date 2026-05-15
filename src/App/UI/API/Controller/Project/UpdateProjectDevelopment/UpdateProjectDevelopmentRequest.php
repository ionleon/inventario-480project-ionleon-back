<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\Project\UpdateProjectDevelopment;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class UpdateProjectDevelopmentRequest
{
    public function __construct(
        #[Assert\NotBlank, Assert\Choice(choices: ['PLANNED', 'IN_PROGRESS', 'BLOCKED', 'COMPLETED'])]
        public string $status = 'PLANNED',
        public ?string $notes = null,
        #[Assert\Range(min: 0, max: 100)]
        public int $progress = 0,
    ) {}
}
