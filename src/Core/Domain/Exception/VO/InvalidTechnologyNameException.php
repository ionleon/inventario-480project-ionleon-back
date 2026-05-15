<?php

declare(strict_types=1);

namespace App\Core\Domain\Exception\VO;

use App\Shared\Domain\Model\CustomException;
use App\Shared\Domain\Model\ErrorCode;

final class InvalidTechnologyNameException extends CustomException
{
    public function __construct(string $value)
    {
        parent::__construct(
            message: 'TR_INVALID_TECHNOLOGY_NAME',
            errorCode: ErrorCode::INVALID_TECHNOLOGY_NAME,
            messageParams: ['%value%' => $value],
        );
    }
}
