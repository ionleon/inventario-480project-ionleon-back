<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\VO\Link;

use App\Core\Domain\Exception\VO\InvalidLinkUrlException;

final readonly class LinkUrl
{
    private const int MAX_LENGTH = 500;

    private string $value;

    public function __construct(string $value)
    {
        if (strlen($value) > self::MAX_LENGTH || filter_var($value, FILTER_VALIDATE_URL) === false) {
            throw new InvalidLinkUrlException($value);
        }
        $this->value = $value;
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
