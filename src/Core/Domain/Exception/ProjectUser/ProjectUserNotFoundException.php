<?php

declare(strict_types=1);

namespace App\Core\Domain\Exception\ProjectUser;

use App\Shared\Domain\Model\CustomException;
use App\Shared\Domain\Model\ErrorCode;

final class ProjectUserNotFoundException extends CustomException
{
    public function __construct(string $id = '')
    {
        parent::__construct(
            message: 'TR_PROJECT_USER_NOT_FOUND',
            errorCode: ErrorCode::PROJECT_USER_NOT_FOUND,
            messageParams: $id !== '' ? ['%id%' => $id] : [],
        );
    }
}
