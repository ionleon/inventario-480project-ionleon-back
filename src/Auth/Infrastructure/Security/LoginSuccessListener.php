<?php

namespace App\Auth\Infrastructure\Security;

use App\Auth\Application\AuthService;
use App\UserManagement\Domain\AppUser;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;


final class LoginSuccessListener
{

    #Needs further work, doesn't actually work
    public function __construct(
        private readonly AuthService $authManager,
    ) {}

    /**
     * @throws InvalidArgumentException
     * @throws JWTDecodeFailureException
     */
    #[AsEventListener(event: 'lexik_jwt_authentication.on_authentication_success')]
    public function onLoginSuccess(AuthenticationSuccessEvent $event): void
    {
        $user = $event->getUser();
        if (!$user instanceof AppUser) return;

        $this->authManager->forceLogout($user);
    }
}
