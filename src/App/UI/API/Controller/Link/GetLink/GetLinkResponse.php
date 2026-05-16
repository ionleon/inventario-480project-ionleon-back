<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\Link\GetLink;

use App\Core\Domain\Model\Aggregate\Link;

final readonly class GetLinkResponse
{
    public function __construct(
        public string $id,
        public string $projectId,
        public string $url,
        public ?string $label,
        public string $createdAt,
    ) {}

    public static function from(Link $link): self
    {
        return new self(
            id: (string) $link->id(),
            projectId: (string) $link->projectId(),
            url: (string) $link->url(),
            label: $link->label() !== null ? (string) $link->label() : null,
            createdAt: $link->createdAt()->format(\DateTimeInterface::ATOM),
        );
    }
}
