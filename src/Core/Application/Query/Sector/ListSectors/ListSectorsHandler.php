<?php

declare(strict_types=1);

namespace App\Core\Application\Query\Sector\ListSectors;

use App\App\UI\API\Controller\Sector\ListSectors\ListSectorsResponse;
use App\Core\Application\Bus\QueryHandler;
use App\Core\Domain\Model\Repository\SectorRepository;

final readonly class ListSectorsHandler implements QueryHandler
{
    public function __construct(private SectorRepository $repository)
    {
    }

    public function __invoke(ListSectorsQuery $query): ListSectorsResponse
    {
        return ListSectorsResponse::from($this->repository->all());
    }
}
