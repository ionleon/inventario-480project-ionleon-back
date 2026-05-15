<?php

namespace App\Auth\Domain\Service;

interface TokenPayloadExtractorInterface
{
    /**@return array{jti: string, exp: int, ttl: int}|null */
    public function extractFromCurrentRequest(): ?array;
}
