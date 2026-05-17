<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\Client\CreateClient;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateClientRequest
{
    public function __construct(
        #[Assert\NotBlank, Assert\Uuid]
        public string $id,
        #[Assert\NotBlank, Assert\Length(min: 2, max: 120)]
        public string $name,
        #[Assert\NotBlank, Assert\Uuid]
        public string $sectorId,
    ) {
    }
}
