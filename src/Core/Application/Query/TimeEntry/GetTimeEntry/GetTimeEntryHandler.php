<?php

declare(strict_types=1);

namespace App\Core\Application\Query\TimeEntry\GetTimeEntry;

use App\App\UI\API\Controller\TimeEntry\GetTimeEntry\TimeEntryResponse;
use App\Core\Application\Bus\QueryHandler;
use App\Core\Domain\Model\Repository\TimeEntryRepository;
use App\Core\Domain\Model\VO\TimeEntry\TimeEntryId;

final readonly class GetTimeEntryHandler implements QueryHandler
{
    public function __construct(private TimeEntryRepository $repository) {}

    public function __invoke(GetTimeEntryQuery $query): TimeEntryResponse
    {
        $timeEntry = $this->repository->findOneOrFail(new TimeEntryId($query->id));

        return TimeEntryResponse::from($timeEntry);
    }
}
