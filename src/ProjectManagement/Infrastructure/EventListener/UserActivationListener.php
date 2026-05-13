<?php

namespace App\ProjectManagement\Infrastructure\EventListener;

use App\ProjectManagement\Domain\ProjectUser\ProjectUserRepositoryInterface;
use App\UserManagement\Domain\Event\UserActivationChanged;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class UserActivationListener
{
    public function __construct(
        private readonly ProjectUserRepositoryInterface $projectUserRepository,
    ) {}

    #[AsEventListener(event: UserActivationChanged::class)]
    public function onUserActivationChanged(UserActivationChanged $event): void
    {
        $this->projectUserRepository->updateActivationByUserId(
            $event->userId,
            $event->isActive,
        );
    }
}
