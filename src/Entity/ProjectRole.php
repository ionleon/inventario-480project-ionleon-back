<?php

namespace App\Entity;

use App\Repository\ProjectRoleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ProjectRoleRepository::class)]
#[UniqueEntity(fields: ['id'], message: 'This ID already in use.')]
#[UniqueEntity(fields: ['name'], message: 'This name already exists.')]
class ProjectRole
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[Groups(['project:read', 'project_role:read'])]
    private ?Uuid $id = null;

    #[ORM\Column(length: 50)]
    #[Groups(['project:read','project_role:read'])]
    private ?string $name = null;


    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function setId(Uuid $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

}
