<?php

declare(strict_types=1);

namespace App\Core\Domain\Exception\VO;

use App\Shared\Domain\Model\CustomException;
use App\Shared\Domain\Model\ErrorCode;

final class InvalidPhoneException extends CustomException
{
    public function __construct(string $value = '')
    {
        parent::__construct(
            message: 'TR_INVALID_PHONE',
            errorCode: ErrorCode::INVALID_PHONE,
            messageParams: $value !== '' ? ['%value%' => $value] : [],
        );
    }
}
