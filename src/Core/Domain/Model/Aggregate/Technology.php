<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Aggregate;

use App\Core\Domain\AggregateRoot;
use App\Core\Domain\Model\Event\Technology\TechnologyWasCreated;
use App\Core\Domain\Model\VO\Technology\TechnologyId;
use App\Core\Domain\Model\VO\Technology\TechnologyName;

class Technology extends AggregateRoot
{
    private function __construct(
        private readonly TechnologyId $id,
        private TechnologyName $name,
    ) {}

    public static function create(
        TechnologyId $id,
        TechnologyName $name,
    ): self {
        $instance = new self(
            id: $id,
            name: $name,
        );

        $instance->recordEvent(TechnologyWasCreated::from($instance));

        return $instance;
    }

    public function id(): TechnologyId
    {
        return $this->id;
    }

    public function name(): TechnologyName
    {
        return $this->name;
    }
}
