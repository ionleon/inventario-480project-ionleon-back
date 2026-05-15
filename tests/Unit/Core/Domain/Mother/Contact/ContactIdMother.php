<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Mother\Contact;

use App\Core\Domain\Model\VO\Contact\ContactId;

final class ContactIdMother
{
    public static function create(?string $value = null): ContactId
    {
        return new ContactId($value ?? ContactId::generate()->__toString());
    }
}
