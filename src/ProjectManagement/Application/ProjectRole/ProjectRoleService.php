<?php

namespace App\ProjectManagement\Application\ProjectRole;

use App\ProjectManagement\Domain\ProjectRole\ProjectRole;
use App\ProjectManagement\Domain\ProjectRole\ProjectRoleRepositoryInterface;
use App\ProjectManagement\Infrastructure\ProjectRole\DoctrineProjectRoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

class ProjectRoleService
{
    public function __construct(
        private ProjectRoleRepositoryInterface $repository
    ) {}

    public function create(array $data): ProjectRole
    {
        if (!isset($data['id'], $data['name'])) {
            throw new \InvalidArgumentException('Role ID and name are required.');
        }

        $projectRole = new ProjectRole();

        try {
            $projectRole->setId(Uuid::fromString($data['id']));
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException('UUID format invalid.');
        }

        $projectRole->setName($data['name']);

        $this->repository->save($projectRole);

        return $projectRole;
    }

    public function update(ProjectRole $projectRole, array $data): void
    {
        if (isset($data['name'])) {
            $projectRole->setName($data['name']);
        }

        $this->repository->save($projectRole);
    }

    public function delete(ProjectRole $projectRole): void
    {
        $this->repository->delete($projectRole);
    }

}
