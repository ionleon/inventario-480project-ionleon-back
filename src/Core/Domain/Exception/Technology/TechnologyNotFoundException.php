<?php

declare(strict_types=1);

namespace App\Core\Domain\Exception\Technology;

use App\Shared\Domain\Model\CustomException;
use App\Shared\Domain\Model\ErrorCode;

final class TechnologyNotFoundException extends CustomException
{
    public function __construct(string $id = '')
    {
        parent::__construct(
            message: 'TR_TECHNOLOGY_NOT_FOUND',
            errorCode: ErrorCode::TECHNOLOGY_NOT_FOUND,
            messageParams: $id !== '' ? ['%id%' => $id] : [],
        );
    }
}
