<?php

namespace App\Entity;

use App\Repository\ProjectUserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ProjectUserRepository::class)]
class ProjectUser
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups('project:read')]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Project $project = null;

    #[ORM\ManyToOne(cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups('project:read')]
    private ?AppUser $appUser = null;

    #[ORM\ManyToOne(cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups('project:read')]
    private ?ProjectRole $projectRole = null;

    /**
     * @var Collection<int, TimeEntry>
     */
    #[ORM\OneToMany(targetEntity: TimeEntry::class, mappedBy: 'projectUser', orphanRemoval: true)]
    private Collection $timeEntries;

    public function __construct()
    {
        $this->timeEntries = new ArrayCollection();
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

    public function setProject(Project $project): static
    {
        $this->project = $project;

        return $this;
    }

    public function getAppUser(): ?AppUser
    {
        return $this->appUser;
    }

    public function setAppUser(AppUser $appUser): static
    {
        $this->appUser = $appUser;

        return $this;
    }

    public function getProjectRole(): ?ProjectRole
    {
        return $this->projectRole;
    }

    public function setProjectRole(ProjectRole $projectRole): static
    {
        $this->projectRole = $projectRole;

        return $this;
    }

    /**
     * @return Collection<int, TimeEntry>
     */
    public function getTimeEntries(): Collection
    {
        return $this->timeEntries;
    }

    public function addTimeEntry(TimeEntry $timeEntry): static
    {
        if (!$this->timeEntries->contains($timeEntry)) {
            $this->timeEntries->add($timeEntry);
            $timeEntry->setProjectUser($this);
        }

        return $this;
    }

    public function removeTimeEntry(TimeEntry $timeEntry): static
    {
        if ($this->timeEntries->removeElement($timeEntry)) {
            // set the owning side to null (unless already changed)
            if ($timeEntry->getProjectUser() === $this) {
                $timeEntry->setProjectUser(null);
            }
        }

        return $this;
    }
}
