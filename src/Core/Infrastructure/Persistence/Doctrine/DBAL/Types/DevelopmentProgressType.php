<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Persistence\Doctrine\DBAL\Types;

use App\Core\Domain\Model\VO\Project\DevelopmentProgress;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

final class DevelopmentProgressType extends Type
{
    public const NAME = 'development_progress';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getIntegerTypeDeclarationSQL($column);
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?DevelopmentProgress
    {
        return $value === null ? null : new DevelopmentProgress((int) $value);
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?int
    {
        if ($value === null) {
            return null;
        }
        /** @var DevelopmentProgress $value */
        return $value->value();
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
