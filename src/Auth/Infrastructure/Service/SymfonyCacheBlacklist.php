<?php

namespace App\Auth\Infrastructure\Service;

use App\Auth\Domain\Service\TokenBlacklistInterface;
use Symfony\Contracts\Cache\CacheInterface;

class SymfonyCacheBlacklist implements TokenBlacklistInterface
{
    public function __construct(
      private CacheInterface $blacklistCache
    ) {}

    public function add(string $jti, int $ttl): void
    {
        if ($ttl <= 0 ) return;

        $cacheItem = $this->blacklistCache->getItem('blacklist_' . $jti);
        $cacheItem->set(true);
        $cacheItem->expiresAfter($ttl);
        $this->blacklistCache->save($cacheItem);
    }
}
