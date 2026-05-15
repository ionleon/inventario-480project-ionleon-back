<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Repository;

use App\Core\Domain\Exception\Project\ProjectNotFoundException;
use App\Core\Domain\Model\Aggregate\Project;
use App\Core\Domain\Model\VO\Client\ClientId;
use App\Core\Domain\Model\VO\Project\ProjectId;
use App\Core\Domain\Model\VO\Project\ProjectName;
use App\Shared\Domain\Pagination\PaginatedResult;

interface ProjectRepository
{
    public function add(Project $project): void;

    public function remove(Project $project): void;

    public function find(ProjectId $id): ?Project;

    /** @throws ProjectNotFoundException */
    public function findOneOrFail(ProjectId $id): Project;

    public function findOneByName(ProjectName $name): ?Project;

    /** @return list<Project> */
    public function all(): array;

    public function findByFiltersPaginated(
        ?string $term,
        ?string $clientId,
        ?bool $isActive,
        int $page,
        int $limit,
    ): PaginatedResult;
}
