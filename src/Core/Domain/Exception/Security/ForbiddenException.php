<?php

declare(strict_types=1);

namespace App\Core\Domain\Exception\Security;

use App\Shared\Domain\Model\CustomException;
use App\Shared\Domain\Model\ErrorCode;
use Throwable;

final class ForbiddenException extends CustomException
{
    public function __construct(string $actionType = '', ?Throwable $previous = null)
    {
        parent::__construct(
            message: 'TR_FORBIDDEN_ACCESS',
            errorCode: ErrorCode::FORBIDDEN,
            messageParams: $actionType !== '' ? ['%actionType%' => $actionType] : [],
            previous: $previous,
        );
    }
}
