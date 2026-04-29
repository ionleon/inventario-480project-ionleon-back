<?php

namespace App\Entity;

use App\Enum\Enviroment;
use App\Repository\LinkRepository;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: LinkRepository::class)]
#[UniqueEntity(fields: ['id'], message: 'This ID already in use.')]

class Link
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[Groups('link:read')]
    private ?Uuid $id = null;

    #[ORM\Column(enumType: Enviroment::class)]
    #[Groups('link:read')]
    private ?Enviroment $enviroment = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups('link:read')]
    private ?string $url = null;

    #[ORM\ManyToOne(inversedBy: 'links')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Development $development = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function setId(Uuid $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getEnviroment(): ?Enviroment
    {
        return $this->enviroment;
    }

    public function setEnviroment(Enviroment $enviroment): static
    {
        $this->enviroment = $enviroment;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getDevelopment(): ?Development
    {
        return $this->development;
    }

    public function setDevelopment(?Development $development): static
    {
        $this->development = $development;

        return $this;
    }
}
