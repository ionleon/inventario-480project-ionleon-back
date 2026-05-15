<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Persistence\Doctrine\DBAL\Types;

use App\Core\Domain\Model\VO\Client\ClientName;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

final class ClientNameType extends Type
{
    public const NAME = 'client_name';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL(['length' => 120]);
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?ClientName
    {
        return $value === null ? null : new ClientName((string) $value);
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
