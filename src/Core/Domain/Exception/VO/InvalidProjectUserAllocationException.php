<?php

declare(strict_types=1);

namespace App\Core\Domain\Exception\VO;

use App\Shared\Domain\Model\CustomException;
use App\Shared\Domain\Model\ErrorCode;

final class InvalidProjectUserAllocationException extends CustomException
{
    public function __construct(int $value = -1)
    {
        parent::__construct(
            message: 'TR_INVALID_PROJECT_USER_ALLOCATION',
            errorCode: ErrorCode::INVALID_PROJECT_USER_ALLOCATION,
            messageParams: $value >= 0 ? ['%value%' => (string) $value] : [],
        );
    }
}
