<?php

declare(strict_types=1);

namespace App\Core\Domain\Exception\VO;

use App\Shared\Domain\Model\CustomException;
use App\Shared\Domain\Model\ErrorCode;

final class InvalidRefreshTokenIdException extends CustomException
{
    public function __construct(int|string $value)
    {
        parent::__construct(
            message: 'TR_INVALID_REFRESH_TOKEN_ID',
            errorCode: ErrorCode::INVALID_REFRESH_TOKEN_ID,
            messageParams: ['%value%' => (string) $value],
        );
    }
}
