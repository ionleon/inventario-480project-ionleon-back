<?php

declare(strict_types=1);

namespace App\Core\Domain\Exception\TimeEntry;

use App\Shared\Domain\Model\CustomException;
use App\Shared\Domain\Model\ErrorCode;

final class UserNotAssignedToProjectException extends CustomException
{
    public function __construct()
    {
        parent::__construct(
            message: 'TR_USER_NOT_ASSIGNED_TO_PROJECT',
            errorCode: ErrorCode::PROJECT_USER_NOT_FOUND,
        );
    }
}
