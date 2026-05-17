<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\Link\UpdateLink;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class UpdateLinkRequest
{
    public function __construct(
        #[Assert\NotBlank, Assert\Url]
        public string $url,
        #[Assert\Length(max: 100)]
        public ?string $label = null,
    ) {
    }
}
