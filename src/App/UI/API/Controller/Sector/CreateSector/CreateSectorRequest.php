<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\Sector\CreateSector;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateSectorRequest
{
    public function __construct(
        #[Assert\NotBlank, Assert\Uuid]
        public string $id,
        #[Assert\NotBlank, Assert\Length(min: 2, max: 100)]
        public string $name,
    ) {}
}
