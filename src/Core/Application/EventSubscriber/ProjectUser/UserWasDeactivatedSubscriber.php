<?php

declare(strict_types=1);

namespace App\Core\Application\EventSubscriber\ProjectUser;

use App\Core\Domain\Model\Event\User\UserWasDeactivated;
use App\Core\Domain\Model\Repository\ProjectUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Cascade-deactivates all active ProjectUsers when their User is deactivated.
 *
 * Idempotency contract: ProjectUser::deactivate() is a no-op when already
 * inactive (early-return guard). Re-dispatching this event after a partial
 * failure is safe — see EventDispatcher's transactional caveat.
 */
#[AsMessageHandler(bus: 'event.bus')]
final readonly class UserWasDeactivatedSubscriber
{
    public function __construct(
        private ProjectUserRepository $projectUserRepository,
        private EntityManagerInterface $em,
    ) {
    }

    public function __invoke(UserWasDeactivated $event): void
    {
        $projectUsers = $this->projectUserRepository->findActiveByUser($event->id);
        foreach ($projectUsers as $pu) {
            $pu->deactivate();
        }
        $this->em->flush();
    }
}
