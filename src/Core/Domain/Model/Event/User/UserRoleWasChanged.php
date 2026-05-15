<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Event\User;

use App\Core\Domain\Model\VO\User\UserId;
use App\Shared\Domain\Enum\SystemRole;
use DateTimeImmutable;

final readonly class UserRoleWasChanged
{
    public function __construct(
        public UserId $id,
        public SystemRole $oldRole,
        public SystemRole $newRole,
        public DateTimeImmutable $occurredAt,
    ) {}
}
