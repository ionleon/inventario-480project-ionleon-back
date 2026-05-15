<?php

namespace App\Auth\Infrastructure\EventListener;

use App\Auth\Application\ForceLogout\ForceLogoutCommand;
use App\Auth\Application\ForceLogout\ForceLogoutHandler;
use App\Core\Domain\Model\Aggregate\User as AppUser;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;


final class LoginSuccessListener
{

    #Needs further work, doesn't actually work
    public function __construct(
        private readonly ForceLogoutHandler $handler,
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

        $command = new ForceLogoutCommand($user->getUserIdentifier());
        $this->handler->handle($command);
    }
}
