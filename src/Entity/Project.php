<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'uuid')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 150)]
    private ?string $name = null;


    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $startDate = null;

    #[ORM\ManyToOne(inversedBy: 'projects')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $clientId = null;

    /**
     * @var Collection<int, Development>
     */
    #[ORM\OneToMany(targetEntity: Development::class, mappedBy: 'projectId', orphanRemoval: true)]
    private Collection $developments;

    public function __construct()
    {
        $this->developments = new ArrayCollection();
    }



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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStartDate(): ?\DateTime
    {
        return $this->startDate;
    }

    public function setStartedAt(?\DateTime $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getClientId(): ?Client
    {
        return $this->clientId;
    }

    public function setClientId(?Client $clientId): static
    {
        $this->clientId = $clientId;

        return $this;
    }

    /**
     * @return Collection<int, Development>
     */
    public function getDevelopments(): Collection
    {
        return $this->developments;
    }

    public function addDevelopment(Development $development): static
    {
        if (!$this->developments->contains($development)) {
            $this->developments->add($development);
            $development->setProjectId($this);
        }

        return $this;
    }

    public function removeDevelopment(Development $development): static
    {
        if ($this->developments->removeElement($development)) {
            // set the owning side to null (unless already changed)
            if ($development->getProjectId() === $this) {
                $development->setProjectId(null);
            }
        }

        return $this;
    }

}
