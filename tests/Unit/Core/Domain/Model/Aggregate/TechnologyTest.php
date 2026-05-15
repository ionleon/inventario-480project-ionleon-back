<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Model\Aggregate;

use App\Core\Domain\Model\Aggregate\Technology;
use App\Core\Domain\Model\Event\Technology\TechnologyWasCreated;
use App\Tests\Unit\Core\Domain\Mother\Technology\TechnologyIdMother;
use App\Tests\Unit\Core\Domain\Mother\Technology\TechnologyMother;
use App\Tests\Unit\Core\Domain\Mother\Technology\TechnologyNameMother;
use PHPUnit\Framework\TestCase;

final class TechnologyTest extends TestCase
{
    public function test_GivenValidVOs_WhenCreate_ThenInstanceWithCreatedEvent(): void
    {
        $technology = TechnologyMother::create();
        $events = $technology->pullEvents();

        self::assertCount(1, $events);
        self::assertInstanceOf(TechnologyWasCreated::class, $events[0]);
    }

    public function test_GivenTechnology_WhenGetters_ThenReturnExpectedValues(): void
    {
        $technology = Technology::create(
            id: TechnologyIdMother::create('00000000-0000-4000-8000-000000000001'),
            name: TechnologyNameMother::create('PHP'),
        );

        self::assertSame('00000000-0000-4000-8000-000000000001', (string) $technology->id());
        self::assertSame('PHP', (string) $technology->name());
    }
}
