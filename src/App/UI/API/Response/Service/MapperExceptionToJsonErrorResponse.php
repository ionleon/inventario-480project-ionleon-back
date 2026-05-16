<?php

declare(strict_types=1);

namespace App\App\UI\API\Response\Service;

use App\App\UI\API\Response\Model\JsonContentErrorResponse;
use App\Core\Domain\Exception\Security\ForbiddenException;
use App\Shared\Domain\Model\CustomException;
use App\Shared\Domain\Model\ErrorCode;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Throwable;

final readonly class MapperExceptionToJsonErrorResponse
{
    public function __invoke(Throwable $exception, bool $returnGenericUnexpectedError = false): ?JsonResponse
    {
        // 403 — ForbiddenException is a CustomException, handle first so it gets 403
        if ($exception instanceof ForbiddenException) {
            return $this->build($exception->errorCode, $exception->getMessage(), Response::HTTP_FORBIDDEN);
        }

        // 400 — Payload validation failures (Symfony Validator / MapRequestPayload)
        if ($exception instanceof ValidationFailedException || $this->isUnprocessablePayload($exception)) {
            return new JsonResponse(
                new JsonContentErrorResponse(ErrorCode::PAYLOAD_VALIDATION_FAILED->value, $exception->getMessage()),
                Response::HTTP_BAD_REQUEST,
            );
        }

        // Domain exceptions — map by ErrorCode
        if ($exception instanceof CustomException) {
            $code = $exception->errorCode;
            $status = $this->statusFor($code);
            return $this->build($code, $exception->getMessage(), $status);
        }

        // 401 — Symfony authentication layer
        if ($exception instanceof HttpException && $exception->getStatusCode() === Response::HTTP_UNAUTHORIZED) {
            return new JsonResponse(
                new JsonContentErrorResponse(ErrorCode::FORBIDDEN->value, 'TR_UNAUTHENTICATED'),
                Response::HTTP_UNAUTHORIZED,
            );
        }

        // Unknown Throwable
        if ($returnGenericUnexpectedError) {
            return new JsonResponse(
                new JsonContentErrorResponse(ErrorCode::UNEXPECTED_ERROR->value, ''),
                Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }

        return null;
    }

    private function statusFor(ErrorCode $code): int
    {
        return match (true) {
            str_starts_with($code->value, 'INVALID_'),
            $code === ErrorCode::PAYLOAD_VALIDATION_FAILED,
            $code === ErrorCode::INVALID_PAYLOAD,
            $code === ErrorCode::INVALID_UUID           => Response::HTTP_BAD_REQUEST,
            str_ends_with($code->value, '_NOT_FOUND')   => Response::HTTP_NOT_FOUND,
            str_starts_with($code->value, 'DUPLICATED_') => Response::HTTP_CONFLICT,
            $code === ErrorCode::FORBIDDEN               => Response::HTTP_FORBIDDEN,
            $code === ErrorCode::REFRESH_TOKEN_EXPIRED,
            $code === ErrorCode::REFRESH_TOKEN_REVOKED  => Response::HTTP_UNAUTHORIZED,
            $code === ErrorCode::CONFLICT               => Response::HTTP_CONFLICT,
            default                                     => Response::HTTP_BAD_REQUEST,
        };
    }

    private function isUnprocessablePayload(Throwable $exception): bool
    {
        return $exception instanceof HttpException
            && $exception->getStatusCode() === Response::HTTP_UNPROCESSABLE_ENTITY;
    }

    private function build(ErrorCode $code, string $message, int $status): JsonResponse
    {
        return new JsonResponse(
            new JsonContentErrorResponse($code->value, $message),
            $status,
        );
    }
}
