<?php

namespace App\ProjectManagement\Application\CreateProjectRole;

use App\ProjectManagement\Domain\ProjectRole\ProjectRole;
use App\ProjectManagement\Domain\ProjectRole\ProjectRoleRepositoryInterface;
use Symfony\Component\Uid\Uuid;

final class CreateProjectRoleHandler
{
    public function __construct(
        private readonly ProjectRoleRepositoryInterface $repository,
    ) {}

    public function handle(CreateProjectRoleCommand $command): ProjectRole
    {
        $role = new ProjectRole();
        $role->setId(Uuid::fromString($command->id));
        $role->setName($command->name);

        $this->repository->save($role);

        return $role;
    }
}
