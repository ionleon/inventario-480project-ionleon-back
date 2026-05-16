<?php

declare(strict_types=1);

namespace App\Core\Application\Query\TimeEntry\ListTimeEntriesByProject;

use App\App\UI\API\Controller\TimeEntry\GetTimeEntry\TimeEntryResponse;
use App\Core\Application\Bus\QueryHandler;
use App\Core\Domain\Model\Aggregate\TimeEntry;
use App\Core\Domain\Model\Repository\TimeEntryRepository;
use App\Core\Domain\Model\VO\Project\ProjectId;

final readonly class ListTimeEntriesByProjectHandler implements QueryHandler
{
    public function __construct(private TimeEntryRepository $repository) {}

    /** @return list<TimeEntryResponse> */
    public function __invoke(ListTimeEntriesByProjectQuery $query): array
    {
        $entries = $this->repository->findByProject(new ProjectId($query->projectId));

        return array_map(
            static fn(TimeEntry $te) => TimeEntryResponse::from($te),
            $entries,
        );
    }
}
