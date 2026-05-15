<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Persistence\Doctrine\DBAL\Types;

use App\Core\Domain\Model\VO\Project\ProjectEndDate;
use DateTimeImmutable;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

final class ProjectEndDateType extends Type
{
    public const NAME = 'project_end_date';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getDateTypeDeclarationSQL($column);
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?ProjectEndDate
    {
        if ($value === null) {
            return null;
        }
        $dt = $value instanceof \DateTimeInterface
            ? DateTimeImmutable::createFromInterface($value)
            : new DateTimeImmutable((string) $value);
        return new ProjectEndDate($dt);
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }
        /** @var ProjectEndDate $value */
        return $value->value()->format('Y-m-d');
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
