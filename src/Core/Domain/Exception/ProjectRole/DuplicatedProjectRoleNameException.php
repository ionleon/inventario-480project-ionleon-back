<?php

declare(strict_types=1);

namespace App\Core\Domain\Exception\ProjectRole;

use App\Shared\Domain\Model\CustomException;
use App\Shared\Domain\Model\ErrorCode;

final class DuplicatedProjectRoleNameException extends CustomException
{
    public function __construct(string $name = '')
    {
        parent::__construct(
            message: 'TR_DUPLICATED_PROJECT_ROLE_NAME',
            errorCode: ErrorCode::DUPLICATED_PROJECT_ROLE_NAME,
            messageParams: $name !== '' ? ['%name%' => $name] : [],
        );
    }
}
