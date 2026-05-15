<?php

declare(strict_types=1);

namespace App\App\UI\API\Response\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

final readonly class ExceptionListener
{
    public function __construct(
        private GetCurrentEnvironment $getCurrentEnvironment,
        private MapperExceptionToJsonErrorResponse $mapper,
        private LoggerInterface $logger,
    ) {}

    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $this->logger->error('Unhandled exception', [
            'message' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);

        $env = ($this->getCurrentEnvironment)();
        if ($env === 'dev') {
            return;
        }

        $response = ($this->mapper)($exception, returnGenericUnexpectedError: $env === 'prod');
        if ($response !== null) {
            $event->setResponse($response);
        }
    }
}
