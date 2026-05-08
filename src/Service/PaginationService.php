<?php

namespace App\Service;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Exception;

class PaginationService
{
    /**
     * @throws Exception
     */
    public function paginate(QueryBuilder $qb, int $page = 1, int $limit = 10): Paginator
    {

        $page = max(1, $page);
        $limit = max(1, $limit);

        $qb->setFirstResult(($page -1) * $limit)
            ->setMaxResults($limit);

        return new Paginator($qb);

    }

}
