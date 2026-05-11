<?php

namespace App\ProjectManagement\Domain\Developments\Technology;

use App\ProjectManagement\Domain\Developments\Development;
use App\ProjectManagement\Infrastructure\Developments\Technology\DoctrineTechnologyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: DoctrineTechnologyRepository::class)]
#[UniqueEntity(fields: ['id'], message: 'This ID already in use.')]
#[UniqueEntity(fields: ['name'], message: 'This name already exists.')]
class Technology
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[Groups(['dev:read', 'tech:read'])]
    private ?Uuid $id = null;

    #[ORM\Column(length: 50, unique: true)]
    #[Groups(['dev:read', 'tech:read'])]
    private ?string $name = null;

    /**
     * @var Collection<int, Development>
     */
    #[ORM\OneToMany(targetEntity: Development::class, mappedBy: 'technology')]
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
            $development->setTechnology($this);
        }

        return $this;
    }

    public function removeDevelopment(Development $development): static
    {
        if ($this->developments->removeElement($development)) {
            // set the owning side to null (unless already changed)
            if ($development->getTechnology() === $this) {
                $development->setTechnology(null);
            }
        }

        return $this;
    }
}
