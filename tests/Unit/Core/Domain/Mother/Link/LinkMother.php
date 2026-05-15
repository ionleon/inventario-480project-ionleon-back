<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Mother\Link;

use App\Core\Domain\Model\Aggregate\Link;
use App\Core\Domain\Model\VO\Link\LinkId;
use App\Core\Domain\Model\VO\Link\LinkLabel;
use App\Core\Domain\Model\VO\Link\LinkUrl;
use App\Core\Domain\Model\VO\Project\ProjectId;
use App\Tests\Unit\Core\Domain\Mother\Project\ProjectIdMother;

final class LinkMother
{
    public static function create(
        ?LinkId $id = null,
        ?ProjectId $projectId = null,
        ?LinkUrl $url = null,
        ?LinkLabel $label = null,
    ): Link {
        return Link::create(
            id: $id ?? LinkIdMother::create(),
            projectId: $projectId ?? ProjectIdMother::create(),
            url: $url ?? LinkUrlMother::create(),
            label: $label,
        );
    }
}
