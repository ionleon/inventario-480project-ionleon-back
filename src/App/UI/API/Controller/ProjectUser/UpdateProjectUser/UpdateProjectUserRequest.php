<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\ProjectUser\UpdateProjectUser;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class UpdateProjectUserRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $roleId,
        #[Assert\NotNull]
        #[Assert\Range(min: 0, max: 100)]
        public int $allocation = 100,
    ) {
    }
}
