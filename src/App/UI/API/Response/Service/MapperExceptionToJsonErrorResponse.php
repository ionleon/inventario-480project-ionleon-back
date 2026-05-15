<?php

declare(strict_types=1);

namespace App\App\UI\API\Response\Service;

use App\App\UI\API\Response\Model\JsonContentErrorResponse;
use App\Core\Domain\Exception\Security\ForbiddenException;
use App\Shared\Domain\Model\CustomException;
use App\Shared\Domain\Model\ErrorCode;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final readonly class MapperExceptionToJsonErrorResponse
{
    public function __invoke(Throwable $exception, bool $returnGenericUnexpectedError = false): ?JsonResponse
    {
        if ($exception instanceof ForbiddenException) {
            return $this->build($exception, Response::HTTP_FORBIDDEN);
        }

        if ($exception instanceof CustomException) {
            // Plan 6 (Fase 3) expande este switch con todas las excepciones de dominio
            return $this->build($exception, Response::HTTP_BAD_REQUEST);
        }

        if ($returnGenericUnexpectedError) {
            return new JsonResponse(
                new JsonContentErrorResponse(ErrorCode::UNEXPECTED_ERROR->value, ''),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return null;
    }

    private function build(CustomException $e, int $status): JsonResponse
    {
        return new JsonResponse(
            new JsonContentErrorResponse($e->errorCode->value, $e->getMessage()),
            $status
        );
    }
}
