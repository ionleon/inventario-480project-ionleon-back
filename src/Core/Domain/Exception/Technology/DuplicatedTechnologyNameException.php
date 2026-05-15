<?php

declare(strict_types=1);

namespace App\Core\Domain\Exception\Technology;

use App\Shared\Domain\Model\CustomException;
use App\Shared\Domain\Model\ErrorCode;

final class DuplicatedTechnologyNameException extends CustomException
{
    public function __construct(string $name = '')
    {
        parent::__construct(
            message: 'TR_DUPLICATED_TECHNOLOGY_NAME',
            errorCode: ErrorCode::DUPLICATED_TECHNOLOGY_NAME,
            messageParams: $name !== '' ? ['%name%' => $name] : [],
        );
    }
}
