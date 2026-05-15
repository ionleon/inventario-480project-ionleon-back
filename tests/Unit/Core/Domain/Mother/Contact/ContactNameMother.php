<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Mother\Contact;

use App\Core\Domain\Model\VO\Contact\ContactName;

final class ContactNameMother
{
    public static function create(?string $value = null): ContactName
    {
        return new ContactName($value ?? 'John Doe');
    }
}
