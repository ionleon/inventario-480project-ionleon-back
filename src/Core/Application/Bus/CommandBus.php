<?php

declare(strict_types=1);

namespace App\Core\Application\Bus;

interface CommandBus
{
    public function dispatch(Command $command): void;
}
