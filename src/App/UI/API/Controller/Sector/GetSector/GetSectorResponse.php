<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\Sector\GetSector;

use App\Core\Domain\Model\Aggregate\Sector;

final readonly class GetSectorResponse
{
    public function __construct(
        public string $id,
        public string $name,
    ) {
    }

    public static function from(Sector $sector): self
    {
        return new self(
            id: (string) $sector->id(),
            name: (string) $sector->name(),
        );
    }
}
