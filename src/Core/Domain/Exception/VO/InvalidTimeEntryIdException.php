<?php

declare(strict_types=1);

namespace App\Core\Domain\Exception\VO;

use App\Shared\Domain\Model\CustomException;
use App\Shared\Domain\Model\ErrorCode;

final class InvalidTimeEntryIdException extends CustomException
{
    public function __construct(string $value = '')
    {
        parent::__construct(
            message: 'TR_INVALID_TIME_ENTRY_ID',
            errorCode: ErrorCode::INVALID_TIME_ENTRY_ID,
            messageParams: $value !== '' ? ['%value%' => $value] : [],
        );
    }
}
