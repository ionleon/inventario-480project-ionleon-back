<?php

namespace App\UserManagement\Infrastructure\Http\Response;

use App\UserManagement\Domain\AppUser;

final readonly class GetUserResponse
{
    public string $id;
    public string $name;
    public string $surname;
    public string $email;
    public string $role;
    public bool   $isActive;
    public bool   $firstTime;

    private function __construct(AppUser $user)
    {
        $this->id        = (string) $user->getId();
        $this->name      = $user->getName();
        $this->surname   = $user->getSurname();
        $this->email     = $user->getEmail();
        $this->role      = $user->getRole()->value;
        $this->isActive  = $user->isActive();
        $this->firstTime = $user->firstTime();
    }

    public static function fromEntity(AppUser $user): self
    {
        return new self($user);
    }
}
