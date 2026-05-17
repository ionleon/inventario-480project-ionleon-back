<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Aggregate;

use App\Core\Domain\AggregateRoot;
use App\Core\Domain\Model\Event\User\UserPasswordWasChanged;
use App\Core\Domain\Model\Event\User\UserRoleWasChanged;
use App\Core\Domain\Model\Event\User\UserWasActivated;
use App\Core\Domain\Model\Event\User\UserWasCreated;
use App\Core\Domain\Model\Event\User\UserWasDeactivated;
use App\Core\Domain\Model\Event\User\UserWasUpdated;
use App\Core\Domain\Model\VO\Common\Email;
use App\Core\Domain\Model\VO\Common\Password;
use App\Core\Domain\Model\VO\User\UserId;
use App\Core\Domain\Model\VO\User\UserName;
use App\Core\Domain\Model\VO\User\UserSurname;
use App\Shared\Domain\Enum\SystemRole;
use DateTimeImmutable;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class User extends AggregateRoot implements UserInterface, PasswordAuthenticatedUserInterface
{
    private function __construct(
        private UserId $id,
        private Email $email,
        private UserName $name,
        private UserSurname $surname,
        private Password $password,
        private SystemRole $role,
        private bool $isActive,
        private bool $firstTime,
    ) {
    }

    public static function create(
        UserId $id,
        Email $email,
        UserName $name,
        UserSurname $surname,
        Password $password,
        SystemRole $role = SystemRole::EMPLOYEE,
    ): self {
        $instance = new self(
            id: $id,
            email: $email,
            name: $name,
            surname: $surname,
            password: $password,
            role: $role,
            isActive: true,
            firstTime: true,
        );

        $instance->recordEvent(UserWasCreated::from($instance));

        return $instance;
    }

    public function id(): UserId
    {
        return $this->id;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function name(): UserName
    {
        return $this->name;
    }

    public function surname(): UserSurname
    {
        return $this->surname;
    }

    public function role(): SystemRole
    {
        return $this->role;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function firstTime(): bool
    {
        return $this->firstTime;
    }

    public function changePassword(Password $newPassword): void
    {
        $this->password = $newPassword;
        $this->firstTime = false;
        $this->recordEvent(UserPasswordWasChanged::from($this));
    }

    public function resetPasswordByAdmin(Password $newPassword): void
    {
        $this->password = $newPassword;
        $this->firstTime = true;
        $this->recordEvent(UserPasswordWasChanged::from($this));
    }

    public function activate(): void
    {
        if ($this->isActive) {
            return;
        }
        $this->isActive = true;
        $this->recordEvent(UserWasActivated::from($this));
    }

    public function deactivate(): void
    {
        if (!$this->isActive) {
            return;
        }
        $this->isActive = false;
        $this->recordEvent(UserWasDeactivated::from($this));
    }

    public function toggleActivation(): void
    {
        if ($this->isActive) {
            $this->deactivate();
        } else {
            $this->activate();
        }
    }

    public function updateProfile(UserName $name, UserSurname $surname): void
    {
        $this->name = $name;
        $this->surname = $surname;
        $this->recordEvent(UserWasUpdated::from($this));
    }

    public function changeRole(SystemRole $newRole): void
    {
        $oldRole = $this->role;
        $this->role = $newRole;
        $this->recordEvent(new UserRoleWasChanged(
            id: $this->id,
            oldRole: $oldRole,
            newRole: $newRole,
            occurredAt: new DateTimeImmutable(),
        ));
    }

    public function markAsReturning(): void
    {
        $this->firstTime = false;
    }

    // Symfony Security interface methods

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /** @return string[] */
    public function getRoles(): array
    {
        return [$this->role->value];
    }

    public function getPassword(): ?string
    {
        return (string) $this->password;
    }

    /**
     * Required by Symfony's UserInterface. We don't store plain credentials on
     * the aggregate (Password VO already wraps the hashed value), so there's
     * nothing to erase. Annotated to silence the Symfony 7.3 deprecation notice.
     */
    #[\Deprecated]
    public function eraseCredentials(): void
    {
    }
}
