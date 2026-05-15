<?php

declare(strict_types=1);

namespace App\Tests\Unit\App\UI\API\Response\Service;

use App\App\UI\API\Response\Service\ExceptionListener;
use App\App\UI\API\Response\Service\GetCurrentEnvironment;
use App\App\UI\API\Response\Service\MapperExceptionToJsonErrorResponse;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

final class ExceptionListenerSmokeTest extends TestCase
{
    public function test_GivenDependencies_WhenConstructed_ThenInstanceIsCreated(): void
    {
        $listener = new ExceptionListener(
            new GetCurrentEnvironment('dev'),
            new MapperExceptionToJsonErrorResponse(),
            new NullLogger(),
        );
        self::assertInstanceOf(ExceptionListener::class, $listener);
    }
}
