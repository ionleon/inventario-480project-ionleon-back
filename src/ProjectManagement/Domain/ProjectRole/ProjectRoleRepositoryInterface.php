<?php

namespace App\ProjectManagement\Domain\ProjectRole;

interface ProjectRoleRepositoryInterface
{
    public function findById(string $id): ?ProjectRole;

    /** @return ProjectRole[] */
    public function findAll(): array;

    public function save(ProjectRole $role): void;

    public function delete(ProjectRole $role): void;

}
