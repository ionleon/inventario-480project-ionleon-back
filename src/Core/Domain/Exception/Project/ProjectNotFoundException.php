<?php

declare(strict_types=1);

namespace App\Core\Domain\Exception\Project;

use App\Shared\Domain\Model\CustomException;
use App\Shared\Domain\Model\ErrorCode;

final class ProjectNotFoundException extends CustomException
{
    public function __construct(string $id = '')
    {
        parent::__construct(
            message: 'TR_PROJECT_NOT_FOUND',
            errorCode: ErrorCode::PROJECT_NOT_FOUND,
            messageParams: $id !== '' ? ['%id%' => $id] : [],
        );
    }
}
