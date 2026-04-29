<?php

namespace App\Service;

use App\Entity\ProjectRole;
use App\Repository\ProjectRoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

class ProjectRoleManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private ProjectRoleRepository $repository
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

        return $this->save($projectRole, $data);
    }

    public function save(ProjectRole $projectRole, array $data): ProjectRole
    {
        $projectRole->setName($data['name'] ?? $projectRole->getName());

        $this->em->persist($projectRole);
        $this->em->flush();

        return $projectRole;
    }

    public function delete(ProjectRole $projectRole): void
    {
        // Posible implementacion, evitar eliminar roles que ya esten asignados

        $this->em->remove($projectRole);
        $this->em->flush();
    }

}
