<?php

declare(strict_types=1);

namespace App\Core\Domain\Exception\VO;

use App\Shared\Domain\Model\CustomException;
use App\Shared\Domain\Model\ErrorCode;

final class InvalidPasswordException extends CustomException
{
    public function __construct()
    {
        parent::__construct(
            message: 'TR_INVALID_PASSWORD',
            errorCode: ErrorCode::INVALID_PASSWORD,
        );
    }
}
