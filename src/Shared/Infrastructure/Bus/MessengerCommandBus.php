<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Bus;

use App\Core\Application\Bus\Command;
use App\Core\Application\Bus\CommandBus;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

final class MessengerCommandBus implements CommandBus
{
    use HandleTrait;

    public function __construct(MessageBusInterface $commandBus)
    {
        $this->messageBus = $commandBus;
    }

    public function dispatch(Command $command): void
    {
        $this->handle($command);
    }
}
