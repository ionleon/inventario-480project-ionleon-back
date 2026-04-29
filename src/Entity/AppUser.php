<?php

namespace App\Entity;

use App\Enum\SystemRole;
use App\Repository\AppUserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AppUserRepository::class)]
#[UniqueEntity(fields: ['id'], message: 'This ID already in use.')]
#[UniqueEntity(fields: ['email'], message: 'This mail is already in use.')]
class AppUser implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[Assert\Uuid]
    #[Groups(['user:read', 'user:write', 'project:read'])]
    private ?Uuid $id = null;

    #[ORM\Column(length: 100)]
    #[Groups(['user:read', 'user:write', 'user:update', 'project:read', 'time:read'])]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3)]
    private ?string $name = null;

    #[ORM\Column(length: 100)]
    #[Groups(['user:read', 'user:write', 'user:update', 'project:read', 'time:read'])]
    #[Assert\NotBlank]
    private ?string $surname = null;

    #[ORM\Column(length: 150, unique: true)]
    #[Groups(['user:read', 'user:write', 'user:update', 'project:read'])]
    #[Assert\NotBlank(message: "El email no puede estar vacío")]
    #[Assert\Email(message: "El formato del email no es válido")]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 8, minMessage: "La contraseña debe tener al menos 8 caracteres")]
    #[Groups(['user:write'])]
    private ?string $password = null;

    #[ORM\Column]
    #[Groups(['user:read', 'user:write'])]
    private ?bool $firstTime = null;

    #[ORM\Column]
    #[Groups(['user:read', 'project:read'])]
    private ?bool $isActive = null;

    #[ORM\Column(type: 'string', enumType: SystemRole::class)]
    #[Groups(['user:read', 'user:write' , 'user:update'])]
    #[Assert\Type(SystemRole::class)]
    private SystemRole $role = SystemRole::EMPLOYEE;

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

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): static
    {
        $this->surname = $surname;

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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function firstTime(): ?bool
    {
        return $this->firstTime;
    }

    public function setFirstTime(bool $firstTime): static
    {
        $this->firstTime = $firstTime;

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

    public function getRole(): SystemRole
    {
        return $this->role;
    }

    public function setRole(SystemRole $role): static
    {
        $this->role = $role;
        return $this;
    }

    public function getRoles(): array
    {
        return [$this->role->value];
    }

    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }
}
