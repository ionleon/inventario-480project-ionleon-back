<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Bus;

use App\Core\Application\Bus\Query;
use App\Core\Application\Bus\QueryBus;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

final class MessengerQueryBus implements QueryBus
{
    use HandleTrait;

    public function __construct(MessageBusInterface $queryBus)
    {
        $this->messageBus = $queryBus;
    }

    public function ask(Query $query): mixed
    {
        return $this->handle($query);
    }
}
