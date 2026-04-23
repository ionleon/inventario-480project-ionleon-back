<?php
declare(strict_types=1);

namespace App\EventListener;

use App\Entity\AppUser;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\HttpFoundation\RequestStack;

class JWTCreatedListener
{
    public function __construct(
        private readonly RequestStack $requestStack
    ){}

    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        #Cambiar fecha de caducidad más adelante
        $expiration = new \DateTime('+1 day');
        $expiration->setTime(3,0,0);

        /** @var AppUser $user */
        $user = $event->getUser();

        $payload = $event->getData();
        $payload['exp'] = $expiration->getTimestamp();

        $payload['id'] = $user->getId();
        $payload['name'] = $user->getName();
        $payload['surname'] = $user->getSurname();
        $payload['isActive'] = $user->isActive();


        $event->setData($payload);
    }



}
