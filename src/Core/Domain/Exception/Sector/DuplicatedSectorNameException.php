<?php

declare(strict_types=1);

namespace App\Core\Domain\Exception\Sector;

use App\Shared\Domain\Model\CustomException;
use App\Shared\Domain\Model\ErrorCode;

final class DuplicatedSectorNameException extends CustomException
{
    public function __construct(string $name = '')
    {
        parent::__construct(
            message: 'TR_DUPLICATED_SECTOR_NAME',
            errorCode: ErrorCode::DUPLICATED_SECTOR_NAME,
            messageParams: $name !== '' ? ['%name%' => $name] : [],
        );
    }
}
