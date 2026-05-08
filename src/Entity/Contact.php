<?php

namespace App\Entity;

use App\ClientManagement\Domain\Client;
use App\Repository\ContactRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ContactRepository::class)]
#[UniqueEntity(fields: ['id'], message: 'This ID already in use.')]

class Contact
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[Groups(['client:read', 'contact:read'])]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['client:read', 'contact:read'])]
    #[SerializedName('full_name')]
    private ?string $fullName = null;

    #[ORM\Column(length: 30)]
    #[Groups(['client:read', 'contact:read'])]
    #[SerializedName('phone_number')]
    private ?string $phoneNumber = null;


    #[ORM\Column(length: 255)]
    #[Groups(['client:read', 'contact:read'])]
    private ?string $email = null;

    #[ORM\Column]
    #[Groups(['client:read', 'contact:read'])]
    #[SerializedName('is_main')]
    private ?bool $isMain = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['client:read', 'contact:read'])]
    private ?string $note = null;

    #[ORM\ManyToOne(inversedBy: 'contacts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function setId(Uuid $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): static
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function isMain(): ?bool
    {
        return $this->isMain;
    }

    public function setIsMain(bool $isMain): static
    {
        $this->isMain = $isMain;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): static
    {
        $this->note = $note;

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
}
