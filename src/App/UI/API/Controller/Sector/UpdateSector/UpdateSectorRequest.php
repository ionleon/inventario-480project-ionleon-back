<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\Sector\UpdateSector;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class UpdateSectorRequest
{
    public function __construct(
        #[Assert\NotBlank, Assert\Length(min: 2, max: 100)]
        public string $name,
    ) {}
}
