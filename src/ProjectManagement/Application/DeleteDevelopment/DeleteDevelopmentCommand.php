<?php

namespace App\ProjectManagement\Application\DeleteDevelopment;

final readonly class DeleteDevelopmentCommand
{
    public function __construct(
        public string $developmentId,
    ) {}
}
