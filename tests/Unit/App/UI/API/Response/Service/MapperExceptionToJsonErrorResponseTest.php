<?php

declare(strict_types=1);

namespace App\Tests\Unit\App\UI\API\Response\Service;

use App\App\UI\API\Response\Service\MapperExceptionToJsonErrorResponse;
use App\Core\Domain\Exception\RefreshToken\RefreshTokenExpiredException;
use App\Core\Domain\Exception\RefreshToken\RefreshTokenRevokedException;
use App\Core\Domain\Exception\Security\ForbiddenException;
use App\Core\Domain\Exception\User\DuplicatedUserEmailException;
use App\Core\Domain\Exception\User\UserNotFoundException;
use App\Core\Domain\Exception\VO\InvalidSectorIdException;
use App\Shared\Domain\Model\ErrorCode;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\Exception\ValidationFailedException;

final class MapperExceptionToJsonErrorResponseTest extends TestCase
{
    private MapperExceptionToJsonErrorResponse $mapper;

    protected function setUp(): void
    {
        $this->mapper = new MapperExceptionToJsonErrorResponse();
    }

    public function test_GivenInvalidVoException_WhenInvoke_ThenReturns400(): void
    {
        $response = ($this->mapper)(new InvalidSectorIdException('bad-id'));

        self::assertNotNull($response);
        self::assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());

        $body = json_decode((string) $response->getContent(), true);
        self::assertSame(ErrorCode::INVALID_SECTOR_ID->value, $body['code']);
    }

    public function test_GivenRefreshTokenExpiredException_WhenInvoke_ThenReturns401(): void
    {
        $response = ($this->mapper)(new RefreshTokenExpiredException('some-token'));

        self::assertNotNull($response);
        self::assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());

        $body = json_decode((string) $response->getContent(), true);
        self::assertSame(ErrorCode::REFRESH_TOKEN_EXPIRED->value, $body['code']);
    }

    public function test_GivenRefreshTokenRevokedException_WhenInvoke_ThenReturns401(): void
    {
        $response = ($this->mapper)(new RefreshTokenRevokedException('some-token'));

        self::assertNotNull($response);
        self::assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());

        $body = json_decode((string) $response->getContent(), true);
        self::assertSame(ErrorCode::REFRESH_TOKEN_REVOKED->value, $body['code']);
    }

    public function test_GivenForbiddenException_WhenInvoke_ThenReturns403(): void
    {
        $response = ($this->mapper)(new ForbiddenException('test-action'));

        self::assertNotNull($response);
        self::assertSame(Response::HTTP_FORBIDDEN, $response->getStatusCode());

        $body = json_decode((string) $response->getContent(), true);
        self::assertSame(ErrorCode::FORBIDDEN->value, $body['code']);
    }

    public function test_GivenUserNotFoundException_WhenInvoke_ThenReturns404(): void
    {
        $response = ($this->mapper)(new UserNotFoundException('some-id'));

        self::assertNotNull($response);
        self::assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());

        $body = json_decode((string) $response->getContent(), true);
        self::assertSame(ErrorCode::USER_NOT_FOUND->value, $body['code']);
    }

    public function test_GivenDuplicatedEmailException_WhenInvoke_ThenReturns409(): void
    {
        $response = ($this->mapper)(new DuplicatedUserEmailException('test@test.com'));

        self::assertNotNull($response);
        self::assertSame(Response::HTTP_CONFLICT, $response->getStatusCode());

        $body = json_decode((string) $response->getContent(), true);
        self::assertSame(ErrorCode::DUPLICATED_USER_EMAIL->value, $body['code']);
    }

    public function test_GivenValidationFailedException_WhenInvoke_ThenReturns400WithPayloadCode(): void
    {
        $violations = $this->createMock(ConstraintViolationListInterface::class);
        $exception = new ValidationFailedException('payload', $violations);

        $response = ($this->mapper)($exception);

        self::assertNotNull($response);
        self::assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());

        $body = json_decode((string) $response->getContent(), true);
        self::assertSame(ErrorCode::PAYLOAD_VALIDATION_FAILED->value, $body['code']);
    }

    public function test_GivenHttpExceptionUnprocessableEntity_WhenInvoke_ThenReturns400WithPayloadCode(): void
    {
        $exception = new HttpException(Response::HTTP_UNPROCESSABLE_ENTITY, 'Validation failed');

        $response = ($this->mapper)($exception);

        self::assertNotNull($response);
        self::assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());

        $body = json_decode((string) $response->getContent(), true);
        self::assertSame(ErrorCode::PAYLOAD_VALIDATION_FAILED->value, $body['code']);
    }

    public function test_GivenUnknownExceptionWithProdFlag_WhenInvoke_ThenReturns500(): void
    {
        $response = ($this->mapper)(new RuntimeException('boom'), returnGenericUnexpectedError: true);

        self::assertNotNull($response);
        self::assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());

        $body = json_decode((string) $response->getContent(), true);
        self::assertSame(ErrorCode::UNEXPECTED_ERROR->value, $body['code']);
    }

    public function test_GivenUnknownExceptionWithoutProdFlag_WhenInvoke_ThenReturnsNull(): void
    {
        $response = ($this->mapper)(new RuntimeException('boom'), returnGenericUnexpectedError: false);

        self::assertNull($response);
    }
}
