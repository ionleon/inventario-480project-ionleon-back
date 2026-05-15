<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Repository;

use App\Core\Domain\Exception\Link\LinkNotFoundException;
use App\Core\Domain\Model\Aggregate\Link;
use App\Core\Domain\Model\VO\Link\LinkId;
use App\Core\Domain\Model\VO\Project\ProjectId;

interface LinkRepository
{
    public function add(Link $link): void;

    public function remove(Link $link): void;

    public function find(LinkId $id): ?Link;

    /** @throws LinkNotFoundException */
    public function findOneOrFail(LinkId $id): Link;

    /** @return list<Link> */
    public function findByProject(ProjectId $projectId): array;
}
