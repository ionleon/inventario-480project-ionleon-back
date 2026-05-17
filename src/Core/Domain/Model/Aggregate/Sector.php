<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Aggregate;

use App\Core\Domain\AggregateRoot;
use App\Core\Domain\Model\Event\Sector\SectorWasCreated;
use App\Core\Domain\Model\Event\Sector\SectorWasUpdated;
use App\Core\Domain\Model\VO\Sector\SectorId;
use App\Core\Domain\Model\VO\Sector\SectorName;

class Sector extends AggregateRoot
{
    private function __construct(
        private SectorId $id,
        private SectorName $name,
    ) {
    }

    public static function create(
        SectorId $id,
        SectorName $name,
    ): self {
        $instance = new self(
            id: $id,
            name: $name,
        );

        $instance->recordEvent(SectorWasCreated::from($instance));

        return $instance;
    }

    public function id(): SectorId
    {
        return $this->id;
    }

    public function name(): SectorName
    {
        return $this->name;
    }

    public function rename(SectorName $newName): void
    {
        if ($this->name->equals($newName)) {
            return;
        }
        $this->name = $newName;
        $this->recordEvent(SectorWasUpdated::from($this));
    }
}
