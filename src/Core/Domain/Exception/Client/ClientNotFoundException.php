<?php

declare(strict_types=1);

namespace App\Core\Domain\Exception\Client;

use App\Shared\Domain\Model\CustomException;
use App\Shared\Domain\Model\ErrorCode;

final class ClientNotFoundException extends CustomException
{
    public function __construct(string $id = '')
    {
        parent::__construct(
            message: 'TR_CLIENT_NOT_FOUND',
            errorCode: ErrorCode::CLIENT_NOT_FOUND,
            messageParams: $id !== '' ? ['%id%' => $id] : [],
        );
    }
}
