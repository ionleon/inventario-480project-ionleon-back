<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\Link\UpdateLink;

use App\Core\Domain\Exception\Link\LinkNotFoundException;
use App\Core\Domain\Model\VO\Link\LinkId;
use App\Core\Domain\Model\VO\Link\LinkLabel;
use App\Core\Domain\Model\VO\Link\LinkUrl;

interface UpdateLinkServiceInterface
{
    /** @throws LinkNotFoundException */
    public function __invoke(LinkId $id, LinkUrl $url, ?LinkLabel $label): void;
}
