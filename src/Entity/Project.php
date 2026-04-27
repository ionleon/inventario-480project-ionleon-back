<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[Groups(['project:read', 'project:write'])]
    private ?Uuid $id = null;

    #[ORM\Column(length: 150)]
    #[Groups(['project:read', 'project:write'])]
    #[Assert\NotBlank]
    private ?string $name = null;


    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['project:read', 'project:write'])]
    #[Assert\NotBlank]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(['project:read', 'project:write'])]
    private ?\DateTime $startDate = null;

    #[ORM\ManyToOne(inversedBy: 'projects')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['project:read', 'project:write'])]
    #[SerializedName('client_id')]
    private ?Client $client = null;

    /**
     * @var Collection<int, Development>
     */
    #[ORM\OneToMany(targetEntity: Development::class, mappedBy: 'project', orphanRemoval: true)]
    #[Groups(['project:read', 'project:write'])]
    private Collection $developments;

    #[ORM\OneToMany(targetEntity: ProjectUser::class, mappedBy: 'project', orphanRemoval: true)]
    #[Groups(['project:read', 'project:write'])]
    private  Collection $projectUsers;

    #[ORM\Column]
    #[Groups(['project:read', 'project:write'])]
    private ?bool $isActive = null;

    public function __construct()
    {
        $this->developments = new ArrayCollection();
        $this->projectUsers = new ArrayCollection();
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

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

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
            $development->setProject($this);
        }

        return $this;
    }

    public function removeDevelopment(Development $development): static
    {
        if ($this->developments->removeElement($development)) {
            // set the owning side to null (unless already changed)
            if ($development->getProject() === $this) {
                $development->setProject(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ProjectUser>
     */

    /**
     * @return Collection
     */
    public function getProjectUsers(): Collection
    {
        return $this->projectUsers;
    }

    #[Groups(['project:read'])]
    public function getCountProjectUsers() : int
    {
        return $this->projectUsers->count();
    }

    public function addProjectUser(ProjectUser $projectUser): static
    {
        if (!$this->projectUsers->contains($projectUser)) {
            $this->projectUsers->add($projectUser);
            $projectUser->setProject($this);
        }

        return $this;
    }

    public function removeProjectUser(ProjectUser $projectUser): static
    {
        $this->projectUsers->removeElement($projectUser);
        return $this;
    }



    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

}
