<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\ProjectRole\CreateProjectRole;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateProjectRoleRequest
{
    public function __construct(
        #[Assert\NotBlank, Assert\Uuid]
        public string $id,
        #[Assert\NotBlank, Assert\Length(min: 2, max: 80)]
        public string $name,
    ) {}
}
