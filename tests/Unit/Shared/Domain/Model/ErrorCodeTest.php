<?php

declare(strict_types=1);

namespace App\Tests\Unit\Shared\Domain\Model;

use App\Shared\Domain\Model\ErrorCode;
use PHPUnit\Framework\TestCase;

final class ErrorCodeTest extends TestCase
{
    public function test_GivenErrorCode_WhenAccessingCases_ThenAllRequiredCodesExist(): void
    {
        $required = [
            'BAD_REQUEST', 'FORBIDDEN', 'NOT_FOUND', 'CONFLICT',
            'UNEXPECTED_ERROR', 'INVALID_UUID', 'PAYLOAD_VALIDATION_FAILED',
            'INVALID_USER_ID', 'USER_NOT_FOUND', 'DUPLICATED_USER_EMAIL',
            'INVALID_CLIENT_ID', 'CLIENT_NOT_FOUND', 'DUPLICATED_CLIENT_NAME',
            'INVALID_PROJECT_ID', 'PROJECT_NOT_FOUND',
        ];

        $existing = array_map(fn(ErrorCode $c) => $c->value, ErrorCode::cases());

        foreach ($required as $code) {
            self::assertContains($code, $existing, "ErrorCode::$code is missing");
        }
    }
}
