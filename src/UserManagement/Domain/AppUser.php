<?php

namespace App\UserManagement\Domain;

use App\Shared\Domain\Enum\SystemRole;
use App\UserManagement\Infrastructure\Persistence\DoctrineUserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DoctrineUserRepository::class)]
#[UniqueEntity(fields: ['id'], message: 'This ID already in use.')]
#[UniqueEntity(fields: ['email'], message: 'This mail is already in use.')]
class AppUser implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[Assert\Uuid]
    #[Groups(['user:read', 'user:write', 'project:read'])]
    private Uuid $id;

    #[ORM\Column(length: 100)]
    #[Groups(['user:read', 'user:write', 'user:update', 'project:read', 'time:read'])]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3)]
    private string $name;

    #[ORM\Column(length: 100)]
    #[Groups(['user:read', 'user:write', 'user:update', 'project:read', 'time:read'])]
    #[Assert\NotBlank]
    private string $surname;

    #[ORM\Column(length: 150, unique: true)]
    #[Groups(['user:read', 'user:write', 'user:update', 'project:read'])]
    #[Assert\NotBlank(message: "El email no puede estar vacío")]
    #[Assert\Email(message: "El formato del email no es válido")]
    private string $email;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 8, minMessage: "La contraseña debe tener al menos 8 caracteres")]
    #[Groups(['user:write'])]
    private ?string $password = null;

    #[ORM\Column]
    #[Groups(['user:read', 'user:write'])]
    #[SerializedName('first_time')]
    private bool $firstTime = true;

    #[ORM\Column]
    #[Groups(['user:read', 'project:read'])]
    #[SerializedName('is_active')]
    private bool $isActive = true;

    #[ORM\Column(type: 'string', enumType: SystemRole::class)]
    #[Groups(['user:read', 'user:write' , 'user:update'])]
    #[Assert\Type(SystemRole::class)]
    private SystemRole $role = SystemRole::EMPLOYEE;

    public static function create(
        Uuid $id,
        string $email,
        string $name,
        string $surname,
        SystemRole $role = SystemRole::EMPLOYEE,
    ): self {
        $user = new self();
        $user->id = $id;
        $user->email = $email;
        $user->name = $name;
        $user->surname = $surname;
        $user->role = $role;
        $user->firstTime = true;
        $user->isActive = true;

        return $user;
    }

    // Getters (solo lectura)

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSurname(): string
    {
        return $this->surname;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function firstTime(): bool
    {
        return $this->firstTime;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getRole(): SystemRole
    {
        return $this->role;
    }

    public function getRoles(): array
    {
        return [$this->role->value];
    }

    // Único setter público requerido por Symfony

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    // Comportamientos explícitos del dominio

    public function activate(): static
    {
        $this->isActive = true;
        return $this;
    }

    public function deactivate(): static
    {
        $this->isActive = false;
        return $this;
    }

    public function toggleActivation(): static
    {
        $this->isActive = !$this->isActive;
        return $this;
    }

    public function changeRole(SystemRole $role): static
    {
        $this->role = $role;
        return $this;
    }

    public function updateProfile(string $name, string $surname): static
    {
        $this->name = $name;
        $this->surname = $surname;
        return $this;
    }

    public function markAsReturning(): static
    {
        $this->firstTime = false;
        return $this;
    }

    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }
}
