<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\Link\CreateLink;

use App\Core\Domain\Exception\Link\LinkNotFoundException;
use App\Core\Domain\Exception\Project\ProjectNotFoundException;
use App\Core\Domain\Model\Aggregate\Link;
use App\Core\Domain\Model\VO\Link\LinkId;
use App\Core\Domain\Model\VO\Link\LinkLabel;
use App\Core\Domain\Model\VO\Link\LinkUrl;
use App\Core\Domain\Model\VO\Project\ProjectId;

interface CreateLinkServiceInterface
{
    /**
     * @throws ProjectNotFoundException
     */
    public function __invoke(
        LinkId $id,
        ProjectId $projectId,
        LinkUrl $url,
        ?LinkLabel $label,
    ): Link;
}
