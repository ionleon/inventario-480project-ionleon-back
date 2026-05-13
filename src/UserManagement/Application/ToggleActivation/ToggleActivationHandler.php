<?php

namespace App\UserManagement\Application\ToggleActivation;

use App\Auth\Application\AuthService;
use App\UserManagement\Domain\AppUserRepositoryInterface;
use App\UserManagement\Domain\Event\UserActivationChanged;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class ToggleActivationHandler
{
    public function __construct(
        private readonly AppUserRepositoryInterface $userRepository,
        private readonly AuthService               $authService,
        private readonly EventDispatcherInterface  $eventDispatcher,
    ) {}

    public function handle(ToggleActivationCommand $command): void
    {
        $user = $this->userRepository->findById($command->userId);

        if ($user === null) {
            throw new \DomainException('User not found.');
        }

        $newStatus = !$user->isActive();
        $user->setIsActive($newStatus);

        $this->userRepository->save($user);

        $this->eventDispatcher->dispatch(
            new UserActivationChanged($command->userId, $newStatus)
        );

        if ($newStatus === false) {
            $this->authService->forceLogout($user);
        }
    }

}
