<?php

namespace App\ProjectManagement\Infrastructure\Developments\Link;

use App\ProjectManagement\Domain\Developments\Link\Link;
use App\ProjectManagement\Domain\Developments\Link\LinkRepositoryInterface;
use App\ProjectManagement\Domain\Developments\Technology\TechnologyRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Link>
 */
class DoctrineLinkRepository extends ServiceEntityRepository implements LinkRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Link::class);
    }

    public function save(Link $link, bool $flush = true): void
    {
        $this->getEntityManager()->persist($link);
        if ($flush) $this->getEntityManager()->flush();
    }

    public function delete(Link $link): void
    {
        $this->getEntityManager()->remove($link);
        $this->getEntityManager()->flush();
    }

}
