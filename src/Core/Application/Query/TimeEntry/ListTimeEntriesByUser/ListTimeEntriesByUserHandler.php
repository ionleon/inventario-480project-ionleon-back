<?php

declare(strict_types=1);

namespace App\Core\Application\Query\TimeEntry\ListTimeEntriesByUser;

use App\App\UI\API\Controller\TimeEntry\GetTimeEntry\TimeEntryResponse;
use App\Core\Application\Bus\QueryHandler;
use App\Core\Domain\Model\Aggregate\TimeEntry;
use App\Core\Domain\Model\Repository\TimeEntryRepository;
use App\Core\Domain\Model\VO\User\UserId;

final readonly class ListTimeEntriesByUserHandler implements QueryHandler
{
    public function __construct(private TimeEntryRepository $repository)
    {
    }

    /** @return list<TimeEntryResponse> */
    public function __invoke(ListTimeEntriesByUserQuery $query): array
    {
        $entries = $this->repository->findByUser(new UserId($query->userId));

        return array_map(
            static fn(TimeEntry $te) => TimeEntryResponse::from($te),
            $entries,
        );
    }
}
