<?php

declare(strict_types=1);

namespace App\App\UI\API\Response\Service;

final readonly class GetCurrentEnvironment
{
    public function __construct(private string $environment)
    {
    }

    public function __invoke(): string
    {
        return $this->environment;
    }
}
