<?php

declare(strict_types=1);

namespace App\App\Auth\Infrastructure\Service;

use App\App\Auth\Domain\Service\TokenBlacklistInterface;
use Symfony\Contracts\Cache\CacheInterface;

final class SymfonyCacheBlacklist implements TokenBlacklistInterface
{
    public function __construct(
        private readonly CacheInterface $blacklistCache,
    ) {}

    public function add(string $jti, int $ttl): void
    {
        if ($ttl <= 0) {
            return;
        }

        $cacheItem = $this->blacklistCache->getItem('blacklist_' . $jti);
        $cacheItem->set(true);
        $cacheItem->expiresAfter($ttl);
        $this->blacklistCache->save($cacheItem);
    }
}
