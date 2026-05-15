<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Event\User;

use App\Core\Domain\Model\VO\Common\Email;
use App\Core\Domain\Model\VO\User\UserId;
use App\Shared\Domain\Enum\SystemRole;
use DateTimeImmutable;

final readonly class UserWasCreated
{
    public function __construct(
        public UserId $id,
        public Email $email,
        public SystemRole $role,
        public DateTimeImmutable $occurredAt,
    ) {}

    public static function from(\App\Core\Domain\Model\Aggregate\User $user): self
    {
        return new self(
            id: $user->id(),
            email: $user->email(),
            role: $user->role(),
            occurredAt: new DateTimeImmutable(),
        );
    }
}
