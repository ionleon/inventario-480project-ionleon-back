<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\Project\CreateProject;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateProjectRequest
{
    /**
     * @param list<string> $technologyIds
     */
    public function __construct(
        #[Assert\NotBlank, Assert\Uuid]
        public string $id,
        #[Assert\NotBlank, Assert\Length(min: 2, max: 150)]
        public string $name,
        public ?string $description = null,
        #[Assert\NotBlank, Assert\Uuid]
        public string $clientId = '',
        #[Assert\NotBlank, Assert\Uuid]
        public string $managerId = '',
        #[Assert\All([new Assert\Uuid()])]
        public array $technologyIds = [],
        public ?string $startDate = null,
        public ?string $endDate = null,
    ) {
    }
}
