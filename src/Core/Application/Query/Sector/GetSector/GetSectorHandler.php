<?php

declare(strict_types=1);

namespace App\Core\Application\Query\Sector\GetSector;

use App\App\UI\API\Controller\Sector\GetSector\GetSectorResponse;
use App\Core\Application\Bus\QueryHandler;
use App\Core\Domain\Model\Repository\SectorRepository;
use App\Core\Domain\Model\VO\Sector\SectorId;

final readonly class GetSectorHandler implements QueryHandler
{
    public function __construct(private SectorRepository $repository) {}

    public function __invoke(GetSectorQuery $query): GetSectorResponse
    {
        $sector = $this->repository->findOneOrFail(new SectorId($query->id));

        return GetSectorResponse::from($sector);
    }
}
