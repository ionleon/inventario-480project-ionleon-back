<?php

declare(strict_types=1);

namespace App\App\Auth\Domain\Service;

use App\Core\Application\DTO\Security\SecurityToken;

interface SecurityTokenExtractorInterface
{
    public function __invoke(): SecurityToken;
}
