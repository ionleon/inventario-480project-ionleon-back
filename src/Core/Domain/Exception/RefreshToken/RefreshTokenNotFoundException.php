<?php

declare(strict_types=1);

namespace App\Core\Domain\Exception\RefreshToken;

use App\Shared\Domain\Model\CustomException;
use App\Shared\Domain\Model\ErrorCode;

final class RefreshTokenNotFoundException extends CustomException
{
    public function __construct(string $identifier)
    {
        parent::__construct(
            message: 'TR_REFRESH_TOKEN_NOT_FOUND',
            errorCode: ErrorCode::REFRESH_TOKEN_NOT_FOUND,
            messageParams: ['%value%' => $identifier],
        );
    }
}
