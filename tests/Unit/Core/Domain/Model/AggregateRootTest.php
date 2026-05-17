<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Model;

use App\Core\Domain\AggregateRoot;
use PHPUnit\Framework\TestCase;

final class AggregateRootTest extends TestCase
{
    public function test_GivenAggregate_WhenRecordEvent_ThenPullEventsReturnsAndClears(): void
    {
        $aggregate = new class extends AggregateRoot {
            public function fire(object $event): void
            {
                $this->recordEvent($event);
            }
        };

        $event1 = new \stdClass();
        $event2 = new \stdClass();

        $aggregate->fire($event1);
        $aggregate->fire($event2);

        $pulled = $aggregate->pullEvents();

        self::assertSame([$event1, $event2], $pulled);
        self::assertSame([], $aggregate->pullEvents());
    }
}
