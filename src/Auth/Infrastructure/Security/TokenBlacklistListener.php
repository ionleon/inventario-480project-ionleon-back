<?php

namespace App\Auth\Infrastructure\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTAuthenticatedEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Contracts\Cache\CacheInterface;


final class TokenBlacklistListener
{
    public function __construct(
        private readonly CacheInterface $blacklistCache
    ) {}

    #[AsEventListener(event: 'lexik_jwt_authentication.on_jwt_authenticated')]
    public function onJwtAuthenticated(JWTAuthenticatedEvent $event): void
    {
        $payload = $event->getPayload();

        if (isset($payload['jti'])) {
            $cacheItem = $this->blacklistCache->getItem('blacklist_' . $payload['jti']);

            if ($cacheItem->isHit()) {
                throw new AccessDeniedException('This token has been revoked.');
            }
        }
    }
}
