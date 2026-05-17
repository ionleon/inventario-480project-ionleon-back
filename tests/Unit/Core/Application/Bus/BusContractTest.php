<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Application\Bus;

use App\Core\Application\Bus\Command;
use App\Core\Application\Bus\CommandBus;
use App\Core\Application\Bus\CommandHandler;
use App\Core\Application\Bus\Query;
use App\Core\Application\Bus\QueryBus;
use App\Core\Application\Bus\QueryHandler;
use PHPUnit\Framework\TestCase;

final class BusContractTest extends TestCase
{
    public function test_GivenMarkerInterfaces_WhenImplemented_ThenInstanceofPasses(): void
    {
        $cmd = new class implements Command {
        };
        $qry = new class implements Query {
        };
        $cmdH = new class implements CommandHandler {
        };
        $qryH = new class implements QueryHandler {
        };

        self::assertInstanceOf(Command::class, $cmd);
        self::assertInstanceOf(Query::class, $qry);
        self::assertInstanceOf(CommandHandler::class, $cmdH);
        self::assertInstanceOf(QueryHandler::class, $qryH);
    }

    public function test_GivenBusInterfaces_WhenInspected_ThenExpectedMethodsExist(): void
    {
        // Reflection check — `method_exists` would be statically known and
        // optimized away by phpstan as "always true". Reflection is dynamic.
        $cmdBus = new \ReflectionClass(CommandBus::class);
        $qryBus = new \ReflectionClass(QueryBus::class);
        self::assertTrue($cmdBus->hasMethod('dispatch'));
        self::assertTrue($qryBus->hasMethod('ask'));
    }
}
