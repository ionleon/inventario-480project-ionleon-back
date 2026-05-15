<?php

declare(strict_types=1);

namespace App\Core\Domain\Exception\VO;

use App\Shared\Domain\Model\CustomException;
use App\Shared\Domain\Model\ErrorCode;

final class InvalidLinkUrlException extends CustomException
{
    public function __construct(string $value = '')
    {
        parent::__construct(
            message: 'TR_INVALID_LINK_URL',
            errorCode: ErrorCode::INVALID_LINK_URL,
            messageParams: $value !== '' ? ['%value%' => $value] : [],
        );
    }
}
