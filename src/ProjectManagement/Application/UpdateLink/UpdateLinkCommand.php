<?php

namespace App\ProjectManagement\Application\UpdateLink;

final readonly class UpdateLinkCommand
{
    public function __construct(
        public string $linkId,
        public ?string $url,
        public ?string $enviroment,
        public ?string $developmentId,
    ) {}
}
