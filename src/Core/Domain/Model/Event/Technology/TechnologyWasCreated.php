<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Event\Technology;

use App\Core\Domain\Model\VO\Technology\TechnologyId;
use App\Core\Domain\Model\VO\Technology\TechnologyName;
use DateTimeImmutable;

final readonly class TechnologyWasCreated
{
    public function __construct(
        public TechnologyId $id,
        public TechnologyName $name,
        public DateTimeImmutable $occurredAt,
    ) {
    }

    public static function from(\App\Core\Domain\Model\Aggregate\Technology $technology): self
    {
        return new self(
            id: $technology->id(),
            name: $technology->name(),
            occurredAt: new DateTimeImmutable(),
        );
    }
}
