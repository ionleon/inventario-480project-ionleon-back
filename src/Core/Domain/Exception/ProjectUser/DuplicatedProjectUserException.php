<?php

declare(strict_types=1);

namespace App\Core\Domain\Exception\ProjectUser;

use App\Shared\Domain\Model\CustomException;
use App\Shared\Domain\Model\ErrorCode;

final class DuplicatedProjectUserException extends CustomException
{
    public function __construct()
    {
        parent::__construct(
            message: 'TR_DUPLICATED_PROJECT_USER',
            errorCode: ErrorCode::DUPLICATED_PROJECT_USER,
        );
    }
}
