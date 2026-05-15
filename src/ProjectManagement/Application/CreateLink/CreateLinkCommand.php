<?php

namespace App\ProjectManagement\Application\CreateLink;

final readonly class CreateLinkCommand
{
    public function __construct(
        public string $id,
        public string $url,
        public string $enviroment,
        public string $developmentId,
    ) {}
}
