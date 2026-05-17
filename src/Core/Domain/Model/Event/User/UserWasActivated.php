<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Event\User;

use App\Core\Domain\Model\VO\User\UserId;
use DateTimeImmutable;

final readonly class UserWasActivated
{
    public function __construct(
        public UserId $id,
        public DateTimeImmutable $occurredAt,
    ) {
    }

    public static function from(\App\Core\Domain\Model\Aggregate\User $user): self
    {
        return new self(
            id: $user->id(),
            occurredAt: new DateTimeImmutable(),
        );
    }
}
