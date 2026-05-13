<?php

namespace App\UserManagement\Infrastructure;


use App\ProjectManagement\Domain\ProjectUser\ProjectUser;
use App\Shared\Domain\Pagination\PaginatedResult;
use App\UserManagement\Domain\AppUser;
use App\UserManagement\Domain\AppUserRepositoryInterface;
use App\UserManagement\Domain\UserFilters;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @extends ServiceEntityRepository<AppUser>
 */
class DoctrineUserRepository extends ServiceEntityRepository implements AppUserRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AppUser::class);
    }

    public function findByFilters(UserFilters $filters) : array
    {
        return $this->createFilteredQueryBuilder($filters)
            ->getQuery()
            ->getResult();
    }


    public function findById(string $id): ?AppUser
    {
        return $this->find($id);
    }

    public function findByEmail(string $email): ?AppUser
    {
        return $this->createQueryBuilder('u')
            ->where('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @throws Exception
     */
    public function findByFiltersPaginated(UserFilters $filters, int $page, int $limit): PaginatedResult
    {
        $qb = $this->createFilteredQueryBuilder($filters);

        $qb->setFirstResult(($page -1) * $limit)
            ->setMaxResults($limit);

        $paginator = new Paginator($qb);
        $totalItems = count($paginator);
        $items = iterator_to_array($paginator->getIterator());

        return new PaginatedResult(
            $items,
            $totalItems,
            $page,
            $limit
        );
    }

    #Revisar esto para mas adelante
    public function findUserByProject(string $projectId): array
    {
        return $this->createQueryBuilder('u')
            ->innerJoin('App\ProjectManagement\Domain\ProjectUser\ProjectUser', 'pu', 'ON', 'pu.appUser = u')
            ->innerJoin('pu.project', 'p')
            ->andWhere('p.id = :projectId')
            ->setParameter('projectId', $projectId)
            ->getQuery()
            ->getResult();
    }

    /**
     * @throws Exception
     */


    public function updateUserActivationWithRelation(AppUser $user, bool $isActive) : void {

        $em = $this->getEntityManager();

        $em->beginTransaction();

        try{
            $em->createQueryBuilder()
                ->update(AppUser::class, 'u')
                ->set('u.isActive', ':status')
                ->where('u.id = :userId')
                ->setParameter('status',$isActive)
                ->setParameter('userId',$user)
                ->getQuery()
                ->execute();

            $em->createQueryBuilder()
                ->update(ProjectUser::class, 'pu')
                ->set('pu.isActive', ':status')
                ->where('pu.appUser = :userId')
                ->setParameter('status',$isActive)
                ->setParameter('userId',$user)
                ->getQuery()
                ->execute();

            $em->commit();

            $user->setIsActive(false);

        } catch (Exception $e) {
            $em->rollback();
            throw $e;
        }

    }

    public function createFilteredQueryBuilder(UserFilters $filters): QueryBuilder
    {
        $qb = $this->createQueryBuilder('u');

        if($filters->term) {
            $qb->andWhere('LOWER(u.name) LIKE LOWER(:term) OR LOWER(u.surname) LIKE LOWER(:term) OR LOWER(u.email) LIKE LOWER(:term)')
                ->setParameter('term', '%' . $filters->term . '%');
        }

        if($filters->role) {
            $qb->andWhere('u.role LIKE :role')
                ->setParameter('role', '%'. $filters->role .'%');
        }

        if($filters->isActive !== null) {
            $qb->andWhere('u.isActive = :isActive')
                ->setParameter('isActive', $filters->isActive);
        }

        $qb->orderBy('u.surname' , 'ASC')
            ->addOrderBy('u.name' , 'ASC');

        return $qb;
    }

    public function save(AppUser $user): void
    {
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function delete(AppUser $user): void
    {
        $this->getEntityManager()->remove($user);
        $this->getEntityManager()->flush();
    }


}
