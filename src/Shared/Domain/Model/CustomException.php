<?php

declare(strict_types=1);

namespace App\Shared\Domain\Model;

use Exception;
use Throwable;

class CustomException extends Exception
{
    /** @param array<string,string> $messageParams */
    public function __construct(
        string $message,
        public readonly ErrorCode $errorCode,
        public readonly array $messageParams = [],
        ?Throwable $previous = null,
    ) {
        parent::__construct(message: $message, previous: $previous);
    }
}
