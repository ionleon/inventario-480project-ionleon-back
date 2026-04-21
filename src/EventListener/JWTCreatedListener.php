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
        /** @var AppUser $user */
        $user = $event->getUser();

        $payload = $event->getData();

        $payload['id'] = $user->getId();
        $payload['name'] = $user->getName();
        $payload['surname'] = $user->getSurname();
        $payload['email'] = $user->getEmail();

        $event->setData($payload);
    }



}
