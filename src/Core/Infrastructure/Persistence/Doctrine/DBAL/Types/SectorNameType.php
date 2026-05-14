<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Persistence\Doctrine\DBAL\Types;

use App\Core\Domain\Model\VO\Sector\SectorName;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

final class SectorNameType extends Type
{
    public const NAME = 'sector_name';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL(['length' => 100]);
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?SectorName
    {
        return $value === null ? null : new SectorName((string) $value);
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
