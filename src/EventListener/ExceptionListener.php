<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
        public function onKernerlException(ExceptionEvent $event): void
        {

            $exception = $event->getThrowable();

            $responseData = [
                'error' => 'Internal Server Error',
                'message' => $exception->getMessage(),
                'code' => 500
            ];

            if($exception instanceof HttpExceptionInterface) {
                $responseData['code'] = $exception->getStatusCode();
                $responseData['error'] = 'HTTP Error';
            }

            $response = new JsonResponse($responseData, $responseData['code']);

            $event->setResponse($response);

        }

}
