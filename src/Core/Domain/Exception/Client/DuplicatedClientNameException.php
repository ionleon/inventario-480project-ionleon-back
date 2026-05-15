<?php

declare(strict_types=1);

namespace App\Core\Domain\Exception\Client;

use App\Shared\Domain\Model\CustomException;
use App\Shared\Domain\Model\ErrorCode;

final class DuplicatedClientNameException extends CustomException
{
    public function __construct(string $name = '')
    {
        parent::__construct(
            message: 'TR_DUPLICATED_CLIENT_NAME',
            errorCode: ErrorCode::DUPLICATED_CLIENT_NAME,
            messageParams: $name !== '' ? ['%name%' => $name] : [],
        );
    }
}
