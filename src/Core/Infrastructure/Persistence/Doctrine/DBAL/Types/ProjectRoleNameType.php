<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Persistence\Doctrine\DBAL\Types;

use App\Core\Domain\Model\VO\ProjectRole\ProjectRoleName;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

final class ProjectRoleNameType extends Type
{
    public const NAME = 'project_role_name';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL(['length' => 80]);
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?ProjectRoleName
    {
        return $value === null ? null : new ProjectRoleName((string) $value);
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
