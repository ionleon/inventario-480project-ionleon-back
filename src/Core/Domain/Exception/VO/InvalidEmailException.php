<?php

declare(strict_types=1);

namespace App\Core\Domain\Exception\VO;

use App\Shared\Domain\Model\CustomException;
use App\Shared\Domain\Model\ErrorCode;

final class InvalidEmailException extends CustomException
{
    public function __construct(string $value)
    {
        parent::__construct(
            message: 'TR_INVALID_EMAIL',
            errorCode: ErrorCode::INVALID_EMAIL,
            messageParams: ['%value%' => $value],
        );
    }
}
