<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Persistence\Doctrine\DBAL\Types;

use App\Core\Domain\Model\VO\TimeEntry\TimeEntryHours;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

final class TimeEntryHoursType extends Type
{
    public const NAME = 'time_entry_hours';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return 'NUMERIC(7, 2)';
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?TimeEntryHours
    {
        return $value === null ? null : new TimeEntryHours((string) $value);
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        return $value === null ? null : $value->value();
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
