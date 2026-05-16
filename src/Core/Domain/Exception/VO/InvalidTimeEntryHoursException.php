<?php

declare(strict_types=1);

namespace App\Core\Domain\Exception\VO;

use App\Shared\Domain\Model\CustomException;
use App\Shared\Domain\Model\ErrorCode;

final class InvalidTimeEntryHoursException extends CustomException
{
    public function __construct(string $value = '')
    {
        parent::__construct(
            message: 'TR_INVALID_TIME_ENTRY_HOURS',
            errorCode: ErrorCode::INVALID_TIME_ENTRY_HOURS,
            messageParams: $value !== '' ? ['%value%' => $value] : [],
        );
    }
}
