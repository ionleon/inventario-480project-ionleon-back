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
    private Uuid $id;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['time:read', 'dash:read'])]
    private \DateTime $date;

    #[ORM\Column(type: Types::DECIMAL, precision: 7, scale: 2)]
    #[Groups(['time:read', 'dash:read'])]
    private string $hour;

    #[ORM\Column(length: 150, nullable: true)]
    #[Groups(['time:read', 'dash:read'])]
    private ?string $comment = null;

    #[ORM\ManyToOne(inversedBy: 'timeEntries')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['time:read'])]
    #[SerializedPath('[project]')]
    private ProjectUser $projectUser;

    public static function create(
        Uuid $id,
        \DateTime $date,
        string $hour,
        ProjectUser $projectUser,
        ?string $comment = null,
    ): self {
        $entry = new self();
        $entry->id = $id;
        $entry->date = $date;
        $entry->hour = $hour;
        $entry->projectUser = $projectUser;
        $entry->comment = $comment;

        return $entry;
    }

    // Getters (solo lectura)

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    public function getHour(): string
    {
        return $this->hour;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function getProjectUser(): ProjectUser
    {
        return $this->projectUser;
    }

    #[Groups(['dash:read'])]
    #[SerializedName('project')]
    public function getProjectForDash(): ?Project
    {
        return $this->projectUser?->getProject();
    }

    // Comportamientos explícitos del dominio

    public function updateTime(\DateTime $date, string $hour): static
    {
        $this->date = $date;
        $this->hour = $hour;
        return $this;
    }

    public function updateComment(?string $comment): static
    {
        $this->comment = $comment;
        return $this;
    }

    public function reassignTo(ProjectUser $projectUser): static
    {
        $this->projectUser = $projectUser;
        return $this;
    }
}
