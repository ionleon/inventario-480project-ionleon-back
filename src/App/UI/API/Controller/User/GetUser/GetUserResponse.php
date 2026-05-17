<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\User\GetUser;

use App\Core\Domain\Model\Aggregate\User;

final readonly class GetUserResponse
{
    public function __construct(
        public string $id,
        public string $email,
        public string $name,
        public string $surname,
        public string $role,
        public bool $isActive,
        public bool $firstTime,
    ) {
    }

    public static function from(User $user): self
    {
        return new self(
            id: (string) $user->id(),
            email: (string) $user->email(),
            name: (string) $user->name(),
            surname: (string) $user->surname(),
            role: $user->role()->value,
            isActive: $user->isActive(),
            firstTime: $user->firstTime(),
        );
    }
}
