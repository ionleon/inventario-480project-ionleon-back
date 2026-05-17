<?php

declare(strict_types=1);

namespace App\Core\Application\Command\Link\UpdateLink;

use App\Core\Application\Bus\Command;
use App\Core\Domain\DTO\Security\SecurityToken;

final readonly class UpdateLinkCommand implements Command
{
    public function __construct(
        public SecurityToken $securityToken,
        public string $id,
        public string $url,
        public ?string $label,
    ) {
    }
}
