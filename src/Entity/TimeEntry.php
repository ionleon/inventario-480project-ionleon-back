<?php

namespace App\Entity;

use App\Repository\TimeEntryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: TimeEntryRepository::class)]
#[UniqueEntity(fields: ['id'], message: 'This ID already in use.')]
class TimeEntry
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[Groups(['time:read'])]
    private ?uuid $id = null;

    #[ORM\ManyToOne(inversedBy: 'timeEntries')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['time:read'])]
    private ?ProjectUser $projectUser = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['time:read'])]
    private ?\DateTime $date = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 7, scale: 2)]
    #[Groups(['time:read'])]
    private ?string $hour = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['time:read'])]
    private ?string $comment = null;

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
