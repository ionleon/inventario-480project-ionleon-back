<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\User\UpdateUser;

final readonly class UpdateUserRequest
{
    public function __construct(
        public string $name,
        public string $surname,
    ) {}
}
