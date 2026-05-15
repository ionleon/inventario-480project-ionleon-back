<?php

declare(strict_types=1);

namespace App\Core\Domain\Exception\User;

use App\Shared\Domain\Model\CustomException;
use App\Shared\Domain\Model\ErrorCode;

final class UserNotFoundException extends CustomException
{
    public function __construct(string $id = '')
    {
        parent::__construct(
            message: 'TR_USER_NOT_FOUND',
            errorCode: ErrorCode::USER_NOT_FOUND,
            messageParams: $id !== '' ? ['%id%' => $id] : [],
        );
    }
}
