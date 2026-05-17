<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\ProjectUser\AssignUserToProject;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class AssignUserToProjectRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $userId,
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $roleId,
        #[Assert\Uuid]
        public ?string $id = null,
        #[Assert\NotNull]
        #[Assert\Range(min: 0, max: 100)]
        public int $allocation = 100,
    ) {
    }
}
