<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Mother\Client;

use App\Core\Domain\Model\VO\Client\ClientId;

final class ClientIdMother
{
    public static function create(?string $value = null): ClientId
    {
        return new ClientId($value ?? ClientId::generate()->__toString());
    }
}
