<?php

namespace App\Auth\Infrastructure\Persistence;

use App\Auth\Domain\RefreshToken\RefreshToken;
use App\Auth\Domain\RefreshToken\RefreshTokenRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RefreshToken>
 */
class DoctrineRefreshTokenRepository extends ServiceEntityRepository implements RefreshTokenRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RefreshToken::class);
    }

    public function revokeAllForUser(string $username): void
    {
        $this->createQueryBuilder('r')
            ->delete()
            ->where('r.username = :username')
            ->setParameter('username', $username)
            ->getQuery()
            ->execute();
    }

    public function delete(string $tokenString): void
    {
        $token = $this->findOneBy(['refreshToken' => $tokenString]);
        if ($token) {
            $this->getEntityManager()->remove($token);
            $this->getEntityManager()->flush();
        }
    }
}
