<?php

namespace App\Service;

use App\Dto\PaginationDto;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Exception;

class PaginationService
{
    /**
     * @throws Exception
     */
    public function paginate(QueryBuilder $qb, int $page = 1, int $limit = 10): PaginationDto
    {

        $page = max(1, $page);
        $limit = max(1, $limit);

        $qb->setFirstResult(($page -1) * $limit)
            ->setMaxResults($limit);

        $paginator = new Paginator($qb);
        $totalItems = count($paginator);
        $totalPages = (int) ceil($totalItems / $limit);

        return new PaginationDto(
            $paginator->getIterator(),
            $totalItems,
            $page,
            $limit,
            $totalPages
        );
    }

}
