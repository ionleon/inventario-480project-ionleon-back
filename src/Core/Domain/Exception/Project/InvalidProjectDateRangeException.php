<?php

declare(strict_types=1);

namespace App\Core\Domain\Exception\Project;

use App\Shared\Domain\Model\CustomException;
use App\Shared\Domain\Model\ErrorCode;

final class InvalidProjectDateRangeException extends CustomException
{
    public function __construct()
    {
        parent::__construct(
            message: 'TR_INVALID_PROJECT_DATE_RANGE',
            errorCode: ErrorCode::INVALID_PROJECT_DATE_RANGE,
            messageParams: [],
        );
    }
}
