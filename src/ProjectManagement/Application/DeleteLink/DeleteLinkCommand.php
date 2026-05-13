<?php

namespace App\ProjectManagement\Application\DeleteLink;

final readonly class DeleteLinkCommand
{
    public function __construct(
        public string $linkId,
    ) {}
}
