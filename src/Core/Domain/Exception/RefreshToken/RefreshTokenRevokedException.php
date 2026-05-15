<?php

declare(strict_types=1);

namespace App\Core\Domain\Exception\RefreshToken;

use App\Shared\Domain\Model\CustomException;
use App\Shared\Domain\Model\ErrorCode;

final class RefreshTokenRevokedException extends CustomException
{
    public function __construct(string $token)
    {
        parent::__construct(
            message: 'TR_REFRESH_TOKEN_REVOKED',
            errorCode: ErrorCode::REFRESH_TOKEN_REVOKED,
            messageParams: ['%value%' => $token],
        );
    }
}
