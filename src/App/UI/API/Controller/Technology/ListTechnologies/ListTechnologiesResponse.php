<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\Technology\ListTechnologies;

use App\Core\Domain\Model\Aggregate\Technology;

final readonly class ListTechnologiesResponse
{
    /** @param list<array{id: string, name: string}> $technologies */
    public function __construct(
        public array $technologies,
    ) {
    }

    /** @param list<Technology> $technologies */
    public static function from(array $technologies): self
    {
        return new self(
            technologies: array_map(
                static fn(Technology $t) => [
                    'id' => (string) $t->id(),
                    'name' => (string) $t->name(),
                ],
                $technologies,
            ),
        );
    }
}
