<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Persistence\Doctrine\DBAL\Types;

use App\Core\Domain\Model\VO\TimeEntry\TimeEntryDate;
use DateTimeImmutable;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

final class TimeEntryDateType extends Type
{
    public const NAME = 'time_entry_date';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return 'DATE';
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?TimeEntryDate
    {
        if ($value === null) {
            return null;
        }

        $dt = DateTimeImmutable::createFromFormat('Y-m-d', (string) $value);

        return $dt !== false ? new TimeEntryDate($dt) : null;
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        return $value === null ? null : (string) $value;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
