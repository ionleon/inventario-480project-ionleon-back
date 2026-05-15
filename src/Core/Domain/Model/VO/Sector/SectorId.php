<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\VO\Sector;

use App\Core\Domain\Exception\VO\InvalidSectorIdException;
use Symfony\Component\Uid\Uuid;

final readonly class SectorId
{
    private string $value;

    public function __construct(string $value)
    {
        if (!Uuid::isValid($value)) {
            throw new InvalidSectorIdException($value);
        }
        $this->value = $value;
    }

    public static function generate(): self
    {
        return new self(Uuid::v4()->toRfc4122());
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
