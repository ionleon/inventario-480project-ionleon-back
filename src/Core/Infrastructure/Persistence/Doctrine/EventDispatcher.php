<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Persistence\Doctrine;

use App\Core\Domain\AggregateRoot;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Collects pending events from all managed aggregates after each flush and
 * publishes them on the event bus.
 *
 * Transactional caveat: this listener runs in `postFlush`, AFTER the original
 * transaction has committed. Subscribers that mutate state (e.g. cascade
 * deactivation of ProjectUser when a User is deactivated) run in a SEPARATE
 * transaction. If a subscriber fails, the original write has already been
 * persisted and the cascade has not — leaving the database in a temporarily
 * inconsistent state.
 *
 * Why `postFlush` instead of `onFlush`: switching to onFlush would let the
 * cascade participate in the same transaction, but it forces subscribers to
 * manage UnitOfWork change sets manually and complicates downstream event
 * recursion. Given that our current subscribers are idempotent and the cascade
 * surface is narrow (one cross-aggregate subscriber today), the eventual
 * consistency trade-off is intentional.
 *
 * All subscribers MUST be idempotent. If you add one that is not, replay
 * the cascade by re-issuing the original command (which will pull the event
 * again via `recordEvent`) or move to an onFlush implementation.
 */
final class EventDispatcher
{
    public function __construct(private readonly MessageBusInterface $eventBus)
    {
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        $em = $args->getObjectManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getIdentityMap() as $entitiesOfClass) {
            foreach ($entitiesOfClass as $entity) {
                if (!$entity instanceof AggregateRoot) {
                    continue;
                }
                foreach ($entity->pullEvents() as $event) {
                    $this->eventBus->dispatch($event);
                }
            }
        }
    }
}
