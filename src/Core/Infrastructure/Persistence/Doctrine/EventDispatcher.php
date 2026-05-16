<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Persistence\Doctrine;

use App\Core\Domain\AggregateRoot;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Symfony\Component\Messenger\MessageBusInterface;

final class EventDispatcher
{
    public function __construct(private readonly MessageBusInterface $eventBus) {}

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
