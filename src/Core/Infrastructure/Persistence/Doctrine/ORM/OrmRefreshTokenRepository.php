<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Persistence\Doctrine\ORM;

use App\Core\Domain\Model\Aggregate\RefreshToken;
use App\Core\Domain\Model\Repository\RefreshTokenRepository;
use App\Core\Domain\Model\VO\RefreshToken\RefreshTokenValue;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Gesdinet\JWTRefreshTokenBundle\Doctrine\RefreshTokenRepositoryInterface;

/**
 * ORM repository for RefreshToken aggregate.
 *
 * This class deliberately extends ServiceEntityRepository and implements the
 * bundle's RefreshTokenRepositoryInterface. The bundle's RefreshTokenManager
 * calls `$objectManager->getRepository(RefreshToken::class)` and requires the
 * result to implement RefreshTokenRepositoryInterface — this is not negotiable.
 *
 * @extends ServiceEntityRepository<RefreshToken>
 */
class OrmRefreshTokenRepository extends ServiceEntityRepository implements RefreshTokenRepository, RefreshTokenRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RefreshToken::class);
    }

    public function add(RefreshToken $refreshToken): void
    {
        $this->getEntityManager()->persist($refreshToken);
    }

    public function remove(RefreshToken $refreshToken): void
    {
        $this->getEntityManager()->remove($refreshToken);
    }

    public function findById(int $id): ?RefreshToken
    {
        return $this->find($id);
    }

    public function findOneByValue(RefreshTokenValue $value): ?RefreshToken
    {
        return $this->findOneBy(['refreshToken' => (string) $value]);
    }

    /** @return list<RefreshToken> */
    public function findByUsername(string $username): array
    {
        /** @var list<RefreshToken> */
        return $this->findBy(['username' => $username]);
    }

    // Gesdinet\JWTRefreshTokenBundle\Doctrine\RefreshTokenRepositoryInterface methods

    /**
     * @return iterable<RefreshToken>
     */
    public function findInvalid(?DateTimeInterface $datetime = null): iterable
    {
        $datetime ??= new \DateTime();

        return $this->createQueryBuilder('rt')
            ->where('rt.valid < :datetime')
            ->setParameter('datetime', $datetime)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param positive-int|null $batchSize
     * @param int<0, max>       $offset
     *
     * @return iterable<RefreshToken>
     */
    public function findInvalidBatch(?DateTimeInterface $datetime = null, ?int $batchSize = null, int $offset = 0): iterable
    {
        $datetime ??= new \DateTime();

        $qb = $this->createQueryBuilder('rt')
            ->where('rt.valid < :datetime')
            ->setParameter('datetime', $datetime)
            ->setFirstResult($offset);

        if ($batchSize !== null) {
            $qb->setMaxResults($batchSize);
        }

        return $qb->getQuery()->getResult();
    }
}
