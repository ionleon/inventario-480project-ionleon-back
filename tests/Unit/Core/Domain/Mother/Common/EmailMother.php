<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Mother\Common;

use App\Core\Domain\Model\VO\Common\Email;

final class EmailMother
{
    public static function create(?string $value = null): Email
    {
        return new Email($value ?? 'alice@example.com');
    }
}
