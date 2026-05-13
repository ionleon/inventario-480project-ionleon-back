<?php

namespace App\TimeManagement\Domain;

use App\ProjectManagement\Domain\Project\Project;
use App\ProjectManagement\Domain\ProjectUser\ProjectUser;
use App\TimeManagement\Infrastructure\DoctrineTimeEntryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\SerializedPath;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: DoctrineTimeEntryRepository::class)]
#[UniqueEntity(fields: ['id'], message: 'This ID already in use.')]
class TimeEntry
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[Groups(['time:read', 'dash:read'])]
    private ?Uuid $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['time:read', 'dash:read'])]
    private ?\DateTime $date = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 7, scale: 2)]
    #[Groups(['time:read', 'dash:read'])]
    private ?string $hour = null;

    #[ORM\Column(length: 150, nullable: true)]
    #[Groups(['time:read', 'dash:read'])]
    private ?string $comment = null;

    #[ORM\ManyToOne(inversedBy: 'timeEntries')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['time:read'])]
    #[SerializedPath('[project]')]
    private ?ProjectUser $projectUser = null;


    #[Groups(['dash:read'])]
    #[SerializedName('project')]
    public function getProjectForDash(): ?Project
    {
        return $this->projectUser?->getProject();
    }
    public function getId(): ?uuid
    {
        return $this->id;
    }

    public function setId(Uuid $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getProjectUser(): ?ProjectUser
    {
        return $this->projectUser;
    }

    public function setProjectUser(?ProjectUser $projectUser): static
    {
        $this->projectUser = $projectUser;

        return $this;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getHour(): ?string
    {
        return $this->hour;
    }

    public function setHour(string $hour): static
    {
        $this->hour = $hour;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }
}
