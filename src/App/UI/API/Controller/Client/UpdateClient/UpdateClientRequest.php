<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\Client\UpdateClient;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class UpdateClientRequest
{
    public function __construct(
        #[Assert\NotBlank, Assert\Length(min: 2, max: 120)]
        public string $name,
        #[Assert\NotBlank, Assert\Uuid]
        public string $sectorId,
    ) {}
}
