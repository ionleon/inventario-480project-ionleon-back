<?php

declare(strict_types=1);

namespace App\App\Auth\Domain\Service;

interface TokenPayloadExtractorInterface
{
    /** @return array{jti: string, exp: int, ttl: int}|null */
    public function extractFromCurrentRequest(): ?array;
}
