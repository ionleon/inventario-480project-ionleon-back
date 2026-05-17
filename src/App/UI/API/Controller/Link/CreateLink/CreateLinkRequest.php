<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\Link\CreateLink;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateLinkRequest
{
    public function __construct(
        #[Assert\NotBlank, Assert\Uuid]
        public string $id,
        #[Assert\NotBlank, Assert\Uuid]
        public string $projectId,
        #[Assert\NotBlank, Assert\Url]
        public string $url,
        #[Assert\Length(max: 100)]
        public ?string $label = null,
    ) {
    }
}
