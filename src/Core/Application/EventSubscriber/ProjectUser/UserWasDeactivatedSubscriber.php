<?php

declare(strict_types=1);

namespace App\Core\Application\EventSubscriber\ProjectUser;

use App\Core\Domain\Model\Event\User\UserWasDeactivated;
use App\Core\Domain\Model\Repository\ProjectUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'event.bus')]
final readonly class UserWasDeactivatedSubscriber
{
    public function __construct(
        private ProjectUserRepository $projectUserRepository,
        private EntityManagerInterface $em,
    ) {}

    public function __invoke(UserWasDeactivated $event): void
    {
        $projectUsers = $this->projectUserRepository->findActiveByUser($event->id);
        foreach ($projectUsers as $pu) {
            $pu->deactivate();
        }
        $this->em->flush();
    }
}
