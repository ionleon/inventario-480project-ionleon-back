<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\ProjectRole\CreateProjectRole;

use App\Core\Domain\Exception\ProjectRole\DuplicatedProjectRoleNameException;
use App\Core\Domain\Model\Aggregate\ProjectRole;
use App\Core\Domain\Model\Repository\ProjectRoleRepository;
use App\Core\Domain\Model\VO\ProjectRole\ProjectRoleId;
use App\Core\Domain\Model\VO\ProjectRole\ProjectRoleName;

final readonly class CreateProjectRoleService implements CreateProjectRoleServiceInterface
{
    public function __construct(private ProjectRoleRepository $repository) {}

    /** @throws DuplicatedProjectRoleNameException */
    public function __invoke(ProjectRoleId $id, ProjectRoleName $name): ProjectRole
    {
        if ($this->repository->findOneByName($name) !== null) {
            throw new DuplicatedProjectRoleNameException((string) $name);
        }

        $projectRole = ProjectRole::create(id: $id, name: $name);
        $this->repository->add($projectRole);

        return $projectRole;
    }
}
