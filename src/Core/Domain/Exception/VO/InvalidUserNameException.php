<?php

declare(strict_types=1);

namespace App\Core\Domain\Exception\VO;

use App\Shared\Domain\Model\CustomException;
use App\Shared\Domain\Model\ErrorCode;

final class InvalidUserNameException extends CustomException
{
    public function __construct(string $value)
    {
        parent::__construct(
            message: 'TR_INVALID_USER_NAME',
            errorCode: ErrorCode::INVALID_USER_NAME,
            messageParams: ['%value%' => $value],
        );
    }
}
