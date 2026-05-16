<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Mother\TimeEntry;

use App\Core\Domain\Model\VO\TimeEntry\TimeEntryId;
use Symfony\Component\Uid\Uuid;

final class TimeEntryIdMother
{
    public static function create(?string $value = null): TimeEntryId
    {
        return new TimeEntryId($value ?? Uuid::v4()->toRfc4122());
    }
}
