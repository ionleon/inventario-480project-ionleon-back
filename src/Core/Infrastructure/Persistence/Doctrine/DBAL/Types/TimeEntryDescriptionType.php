<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Persistence\Doctrine\DBAL\Types;

use App\Core\Domain\Model\VO\TimeEntry\TimeEntryDescription;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

final class TimeEntryDescriptionType extends Type
{
    public const NAME = 'time_entry_description';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL(['length' => 500]);
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?TimeEntryDescription
    {
        return ($value === null || $value === '') ? null : new TimeEntryDescription((string) $value);
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
