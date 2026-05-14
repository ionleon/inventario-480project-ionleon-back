<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\Sector\ListSectors;

use App\Core\Domain\Model\Aggregate\Sector;

final readonly class ListSectorsResponse
{
    /** @param list<array{id: string, name: string}> $sectors */
    public function __construct(
        public array $sectors,
    ) {}

    /** @param list<Sector> $sectors */
    public static function from(array $sectors): self
    {
        return new self(
            sectors: array_map(
                static fn(Sector $s) => [
                    'id' => (string) $s->id(),
                    'name' => (string) $s->name(),
                ],
                $sectors,
            ),
        );
    }
}
