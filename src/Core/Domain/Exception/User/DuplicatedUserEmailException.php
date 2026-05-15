<?php

declare(strict_types=1);

namespace App\Core\Domain\Exception\User;

use App\Shared\Domain\Model\CustomException;
use App\Shared\Domain\Model\ErrorCode;

final class DuplicatedUserEmailException extends CustomException
{
    public function __construct(string $email = '')
    {
        parent::__construct(
            message: 'TR_DUPLICATED_USER_EMAIL',
            errorCode: ErrorCode::DUPLICATED_USER_EMAIL,
            messageParams: $email !== '' ? ['%email%' => $email] : [],
        );
    }
}
