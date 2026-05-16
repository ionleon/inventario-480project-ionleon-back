<?php

declare(strict_types=1);

namespace App\Core\Domain\Exception\TimeEntry;

use App\Shared\Domain\Model\CustomException;
use App\Shared\Domain\Model\ErrorCode;

final class TimeEntryNotFoundException extends CustomException
{
    public function __construct(string $id = '')
    {
        parent::__construct(
            message: 'TR_TIME_ENTRY_NOT_FOUND',
            errorCode: ErrorCode::TIME_ENTRY_NOT_FOUND,
            messageParams: $id !== '' ? ['%id%' => $id] : [],
        );
    }
}
