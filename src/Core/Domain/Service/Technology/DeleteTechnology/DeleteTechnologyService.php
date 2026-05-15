<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\Technology\DeleteTechnology;

use App\Core\Domain\Exception\Technology\TechnologyNotFoundException;
use App\Core\Domain\Model\Repository\TechnologyRepository;
use App\Core\Domain\Model\VO\Technology\TechnologyId;

final readonly class DeleteTechnologyService implements DeleteTechnologyServiceInterface
{
    // TODO (Plan 4 — Project slice): Add cross-aggregate check here.
    // Before removing, verify no Project/Development references this Technology. If any
    // Development exists with this technology_id, throw a DomainException blocking deletion.
    // The check will be implemented via the DevelopmentRepository once Development is migrated.

    public function __construct(private TechnologyRepository $repository) {}

    /** @throws TechnologyNotFoundException */
    public function __invoke(TechnologyId $id): void
    {
        $technology = $this->repository->findOneOrFail($id);

        $this->repository->remove($technology);
    }
}
