<?php

declare(strict_types=1);

namespace App\Core\Domain\Exception\VO;

use App\Shared\Domain\Model\CustomException;
use App\Shared\Domain\Model\ErrorCode;

final class InvalidUserSurnameException extends CustomException
{
    public function __construct(string $value)
    {
        parent::__construct(
            message: 'TR_INVALID_USER_SURNAME',
            errorCode: ErrorCode::INVALID_USER_SURNAME,
            messageParams: ['%value%' => $value],
        );
    }
}
