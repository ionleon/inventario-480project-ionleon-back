<?php

declare(strict_types=1);

namespace App\Core\Application\Bus;

interface QueryBus
{
    public function ask(Query $query): mixed;
}
