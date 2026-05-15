<?php

declare(strict_types=1);

namespace App\Core\Domain\Exception\Link;

use App\Shared\Domain\Model\CustomException;
use App\Shared\Domain\Model\ErrorCode;

final class LinkNotFoundException extends CustomException
{
    public function __construct(string $id = '')
    {
        parent::__construct(
            message: 'TR_LINK_NOT_FOUND',
            errorCode: ErrorCode::LINK_NOT_FOUND,
            messageParams: $id !== '' ? ['%id%' => $id] : [],
        );
    }
}
