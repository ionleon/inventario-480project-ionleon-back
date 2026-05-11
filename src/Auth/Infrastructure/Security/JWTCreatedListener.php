<?php
declare(strict_types=1);

namespace App\Auth\Infrastructure\Security;

use App\UserManagement\Domain\AppUser;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Random\RandomException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Uid\Uuid;

class JWTCreatedListener
{
    public function __construct(
        private readonly RequestStack $requestStack
    ){}

    /**
     * @throws RandomException
     */
    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        /** @var AppUser $user */
        $user = $event->getUser();

        $payload = $event->getData();

        $payload['jti'] = Uuid::v7()->toRfc4122();

        #Cambiar fecha de caducidad más adelante
        $expiration = new \DateTime('+15 minutes');


        $payload['exp'] = $expiration->getTimestamp();

        $payload['id'] = $user->getId();
        $payload['name'] = $user->getName();
        $payload['surname'] = $user->getSurname();
        $payload['isActive'] = $user->isActive();


        $event->setData($payload);
    }

}
