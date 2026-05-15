<?php

declare(strict_types=1);

namespace App\Core\Domain;

use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
abstract class AggregateRoot
{
    /** @var list<object> */
    private array $pendingEvents = [];

    protected function recordEvent(object $event): void
    {
        $this->pendingEvents[] = $event;
    }

    /** @return list<object> */
    public function pullEvents(): array
    {
        $events = $this->pendingEvents;
        $this->pendingEvents = [];
        return $events;
    }
}
