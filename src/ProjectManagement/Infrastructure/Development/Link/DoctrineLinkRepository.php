<?php

namespace App\ProjectManagement\Infrastructure\Development\Link;

use App\ProjectManagement\Domain\Development\Link\Link;
use App\ProjectManagement\Domain\Development\Link\LinkRepositoryInterface;
use App\ProjectManagement\Domain\Development\Technology\TechnologyRepositoryInterface;
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
