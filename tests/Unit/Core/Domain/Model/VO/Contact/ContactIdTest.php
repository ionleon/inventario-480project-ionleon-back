<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Model\VO\Contact;

use App\Core\Domain\Exception\VO\InvalidContactIdException;
use App\Core\Domain\Model\VO\Contact\ContactId;
use PHPUnit\Framework\TestCase;

final class ContactIdTest extends TestCase
{
    public function test_GivenValidUuid_WhenConstruct_ThenSuccess(): void
    {
        $id = new ContactId('00000000-0000-4000-8000-000000000001');
        self::assertSame('00000000-0000-4000-8000-000000000001', (string) $id);
    }

    public function test_GivenInvalidUuid_WhenConstruct_ThenThrowsInvalidContactIdException(): void
    {
        $this->expectException(InvalidContactIdException::class);
        new ContactId('not-a-uuid');
    }

    public function test_Generate_WhenCalled_ThenReturnsValidContactId(): void
    {
        $id = ContactId::generate();
        self::assertInstanceOf(ContactId::class, $id);
        self::assertNotEmpty((string) $id);
    }

    public function test_Equals_WhenSameValue_ThenTrue(): void
    {
        $a = new ContactId('00000000-0000-4000-8000-000000000001');
        $b = new ContactId('00000000-0000-4000-8000-000000000001');
        self::assertTrue($a->equals($b));
    }

    public function test_Equals_WhenDifferentValue_ThenFalse(): void
    {
        $a = new ContactId('00000000-0000-4000-8000-000000000001');
        $b = new ContactId('00000000-0000-4000-8000-000000000002');
        self::assertFalse($a->equals($b));
    }
}
