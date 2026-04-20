<?php

namespace App\Entity;

use App\Repository\TechnologyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: TechnologyRepository::class)]
class Technology
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'uuid')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    /**
     * @var Collection<int, Development>
     */
    #[ORM\OneToMany(targetEntity: Development::class, mappedBy: 'technologyId')]
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
            $development->setTechnologyId($this);
        }

        return $this;
    }

    public function removeDevelopment(Development $development): static
    {
        if ($this->developments->removeElement($development)) {
            // set the owning side to null (unless already changed)
            if ($development->getTechnologyId() === $this) {
                $development->setTechnologyId(null);
            }
        }

        return $this;
    }
}
