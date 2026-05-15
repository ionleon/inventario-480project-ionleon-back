<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Model\VO\Client;

use App\Core\Domain\Exception\VO\InvalidClientIdException;
use App\Core\Domain\Model\VO\Client\ClientId;
use PHPUnit\Framework\TestCase;

final class ClientIdTest extends TestCase
{
    public function test_GivenValidUuid_WhenConstruct_ThenCreated(): void
    {
        $id = new ClientId('00000000-0000-4000-8000-000000000001');
        self::assertSame('00000000-0000-4000-8000-000000000001', (string) $id);
    }

    public function test_GivenInvalidString_WhenConstruct_ThenThrows(): void
    {
        $this->expectException(InvalidClientIdException::class);
        new ClientId('not-a-uuid');
    }

    public function test_WhenGenerate_ThenValidUuid(): void
    {
        $id = ClientId::generate();
        self::assertNotEmpty((string) $id);
        self::assertSame((string) $id, (string) new ClientId((string) $id));
    }

    public function test_GivenSameValue_WhenEquals_ThenTrue(): void
    {
        $a = new ClientId('00000000-0000-4000-8000-000000000001');
        $b = new ClientId('00000000-0000-4000-8000-000000000001');
        self::assertTrue($a->equals($b));
    }

    public function test_GivenDifferentValue_WhenEquals_ThenFalse(): void
    {
        $a = new ClientId('00000000-0000-4000-8000-000000000001');
        $b = new ClientId('00000000-0000-4000-8000-000000000002');
        self::assertFalse($a->equals($b));
    }
}
