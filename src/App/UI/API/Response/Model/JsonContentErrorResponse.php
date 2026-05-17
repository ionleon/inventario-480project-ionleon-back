<?php

declare(strict_types=1);

namespace App\App\UI\API\Response\Model;

final readonly class JsonContentErrorResponse
{
    public function __construct(
        public string $code,
        public string $message = '',
    ) {
    }
}
