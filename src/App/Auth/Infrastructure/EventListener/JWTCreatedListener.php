<?php

declare(strict_types=1);

namespace App\App\Auth\Infrastructure\EventListener;

use App\Core\Domain\Model\Aggregate\User as AppUser;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\Uid\Uuid;

final class JWTCreatedListener
{
    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        /** @var AppUser $user */
        $user = $event->getUser();

        $payload = $event->getData();

        $payload['jti'] = Uuid::v7()->toRfc4122();

        $expiration = new \DateTime('+15 minutes');
        $payload['exp'] = $expiration->getTimestamp();

        $payload['id'] = (string) $user->id();
        $payload['name'] = (string) $user->name();
        $payload['surname'] = (string) $user->surname();
        $payload['isActive'] = $user->isActive();

        $event->setData($payload);
    }
}
