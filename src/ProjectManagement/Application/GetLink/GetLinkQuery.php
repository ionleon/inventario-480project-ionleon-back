<?php

namespace App\ProjectManagement\Application\GetLink;

final readonly class GetLinkQuery
{
    public function __construct(
        public string $linkId,
    ) {}
}
