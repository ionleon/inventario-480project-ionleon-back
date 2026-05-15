<?php

namespace App\TimeManagement\Application\ListTimeEntriesByUser;

use App\TimeManagement\Domain\TimeEntryRepositoryInterface;
use App\UserManagement\Domain\AppUserRepositoryInterface;

final class ListTimeEntriesByUserHandler
{
    public function __construct(
        private readonly TimeEntryRepositoryInterface $repository,
        private readonly AppUserRepositoryInterface $userRepository,
    ) {}

    public function handle(ListTimeEntriesByUserQuery $query): array
    {
        $user = $this->userRepository->findById($query->userId);

        if (!$user) {
            throw new \DomainException('User not found');
        }

        $entries = $this->repository->findByUserPaginated($user, $query->page, $query->limit);
        $totalHours = $this->repository->getTotalHoursByUser($user);

        return [
            'total_hours' => $totalHours,
            'data' => $entries,
        ];
    }
}
