<?php

declare(strict_types=1);

namespace App\Core\Application\Command\Link\CreateLink;

use App\Core\Application\Bus\Command;
use App\Core\Domain\DTO\Security\SecurityToken;

final readonly class CreateLinkCommand implements Command
{
    public function __construct(
        public SecurityToken $securityToken,
        public string $id,
        public string $projectId,
        public string $url,
        public ?string $label,
    ) {
    }
}
