<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Mother\Client;

use App\Core\Domain\Model\VO\Client\ClientName;
use Faker\Factory;

final class ClientNameMother
{
    public static function create(?string $value = null): ClientName
    {
        return new ClientName($value ?? Factory::create()->company());
    }
}
