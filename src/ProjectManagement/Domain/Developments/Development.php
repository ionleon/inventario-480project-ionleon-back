<?php

namespace App\ProjectManagement\Domain\Developments;

use App\ProjectManagement\Domain\Developments\Link\Link;
use App\ProjectManagement\Domain\Developments\Technology\Technology;
use App\ProjectManagement\Domain\Project\Project;
use App\ProjectManagement\Infrastructure\Developments\DoctrineDevelopmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: DoctrineDevelopmentRepository::class)]
#[UniqueEntity(fields: ['id'], message: 'This ID already in use.')]

class Development
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[Groups(['dev:read'])]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(inversedBy: 'developments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Project $project = null;

    #[ORM\ManyToOne(inversedBy: 'developments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['dev:read'])]
    private ?Technology $technology = null;

    #[ORM\Column(length: 100, unique: true)]
    #[Groups(['dev:read'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['dev:read'])]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['dev:read'])]
    #[SerializedName('url_repository')]
    private ?string $urlRepository = null;

    /**
     * @var Collection<int, Link>
     */
    #[ORM\OneToMany(targetEntity: Link::class, mappedBy: 'development', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $links;

    public function __construct()
    {
        $this->links = new ArrayCollection();
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

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): static
    {
        $this->project = $project;

        return $this;
    }

    public function getTechnology(): ?Technology
    {
        return $this->technology;
    }

    public function setTechnology(?Technology $technology): static
    {
        $this->technology = $technology;

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

    public function getUrlRepository(): ?string
    {
        return $this->urlRepository;
    }

    public function setUrlRepository(string $urlRepository): static
    {
        $this->urlRepository = $urlRepository;

        return $this;
    }

    /**
     * @return Collection<int, Link>
     */
    public function getLinks(): Collection
    {
        return $this->links;
    }

    public function addLink(Link $link): static
    {
        if (!$this->links->contains($link)) {
            $this->links->add($link);
            $link->setDevelopment($this);
        }

        return $this;
    }

    public function removeLink(Link $link): static
    {
        if ($this->links->removeElement($link)) {
            // set the owning side to null (unless already changed)
            if ($link->getDevelopment() === $this) {
                $link->setDevelopment(null);
            }
        }

        return $this;
    }
}
