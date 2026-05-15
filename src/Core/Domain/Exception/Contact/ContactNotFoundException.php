<?php

declare(strict_types=1);

namespace App\Core\Domain\Exception\Contact;

use App\Shared\Domain\Model\CustomException;
use App\Shared\Domain\Model\ErrorCode;

final class ContactNotFoundException extends CustomException
{
    public function __construct(string $id = '')
    {
        parent::__construct(
            message: 'TR_CONTACT_NOT_FOUND',
            errorCode: ErrorCode::CONTACT_NOT_FOUND,
            messageParams: $id !== '' ? ['%id%' => $id] : [],
        );
    }
}
