<?php

namespace App\ProjectManagement\Application\ListProjectUsers;

use App\ProjectManagement\Domain\ProjectUser\ProjectUser;

final readonly class ProjectUserDTO
{
    public function __construct(
        public string $appUserId,
        public string $name,
        public string $surname,
        public bool $isUserActive,
        public bool $isAssignmentActive,
        public array $role,
    ) {}

    public static function fromEntity(ProjectUser $projectUser): self
    {
        $appUser = $projectUser->getAppUser();
        $role = $projectUser->getProjectRole();

        return new self(
            appUserId: $appUser->getId()->toRfc4122(),
            name: $appUser->getName() ?? '',
            surname: $appUser->getSurname() ?? '',
            isUserActive: $appUser->isActive() ?? false,
            isAssignmentActive: $projectUser->isActive() ?? false,
            role: [
                'id' => $role?->getId()?->toRfc4122() ?? '',
                'name' => $role?->getName() ?? '',
            ],
        );
    }

    public function toArray(): array
    {
        return [
            'app_user_id' => $this->appUserId,
            'name' => $this->name,
            'surname' => $this->surname,
            'is_user_active' => $this->isUserActive,
            'is_assignment_active' => $this->isAssignmentActive,
            'role' => $this->role,
        ];
    }
}
