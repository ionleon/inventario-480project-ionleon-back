<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Model\VO\Sector;

use App\Core\Domain\Exception\VO\InvalidSectorIdException;
use App\Core\Domain\Model\VO\Sector\SectorId;
use PHPUnit\Framework\TestCase;

final class SectorIdTest extends TestCase
{
    public function test_GivenValidUuid_WhenConstruct_ThenInstanceIsCreated(): void
    {
        $id = new SectorId('00000000-0000-4000-8000-000000000000');
        self::assertSame('00000000-0000-4000-8000-000000000000', (string) $id);
    }

    public function test_GivenInvalidUuid_WhenConstruct_ThenThrowsInvalidSectorIdException(): void
    {
        $this->expectException(InvalidSectorIdException::class);
        new SectorId('not-a-uuid');
    }

    public function test_GivenNull_WhenGenerate_ThenInstanceIsCreated(): void
    {
        $id = SectorId::generate();
        self::assertNotEmpty((string) $id);
    }

    public function test_GivenSameValue_WhenEquals_ThenTrue(): void
    {
        $a = new SectorId('00000000-0000-4000-8000-000000000001');
        $b = new SectorId('00000000-0000-4000-8000-000000000001');
        self::assertTrue($a->equals($b));
    }

    public function test_GivenDifferentValue_WhenEquals_ThenFalse(): void
    {
        $a = new SectorId('00000000-0000-4000-8000-000000000001');
        $b = new SectorId('00000000-0000-4000-8000-000000000002');
        self::assertFalse($a->equals($b));
    }
}
