<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Persistence\Doctrine\DBAL\Types;

use App\Core\Domain\Model\VO\ProjectUser\ProjectUserId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

final class ProjectUserIdType extends Type
{
    public const NAME = 'project_user_id';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL(['length' => 36, 'fixed' => true]);
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?ProjectUserId
    {
        return $value === null ? null : new ProjectUserId((string) $value);
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
