<?php

declare(strict_types=1);

namespace App\Core\Domain\Exception\Sector;

use App\Shared\Domain\Model\CustomException;
use App\Shared\Domain\Model\ErrorCode;

final class SectorNotFoundException extends CustomException
{
    public function __construct(string $id = '')
    {
        parent::__construct(
            message: 'TR_SECTOR_NOT_FOUND',
            errorCode: ErrorCode::SECTOR_NOT_FOUND,
            messageParams: $id !== '' ? ['%id%' => $id] : [],
        );
    }
}
