<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\Link\DeleteLink;

use App\Core\Domain\Exception\Link\LinkNotFoundException;
use App\Core\Domain\Model\VO\Link\LinkId;

interface DeleteLinkServiceInterface
{
    /** @throws LinkNotFoundException */
    public function __invoke(LinkId $id): void;
}
