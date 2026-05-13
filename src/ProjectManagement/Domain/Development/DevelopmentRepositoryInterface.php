<?php

namespace App\ProjectManagement\Domain\Development;

use App\ProjectManagement\Domain\Project\Project;

interface DevelopmentRepositoryInterface
{
    public function findById(string $id): ?Development;

    /** @return Development[] */
    public function findByProject(Project $project): array;

    public function save(Development $development): void;

    public function delete(Development $development): void;
}
