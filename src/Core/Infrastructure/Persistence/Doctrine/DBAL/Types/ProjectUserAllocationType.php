<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Persistence\Doctrine\DBAL\Types;

use App\Core\Domain\Model\VO\ProjectUser\ProjectUserAllocation;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

final class ProjectUserAllocationType extends Type
{
    public const NAME = 'project_user_allocation';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return 'SMALLINT';
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?ProjectUserAllocation
    {
        return $value === null ? null : new ProjectUserAllocation((int) $value);
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?int
    {
        return $value === null ? null : $value->value();
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
