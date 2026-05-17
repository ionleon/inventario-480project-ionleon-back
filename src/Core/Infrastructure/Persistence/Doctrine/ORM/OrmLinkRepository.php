<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Persistence\Doctrine\ORM;

use App\Core\Domain\Exception\Link\LinkNotFoundException;
use App\Core\Domain\Model\Aggregate\Link;
use App\Core\Domain\Model\Repository\LinkRepository;
use App\Core\Domain\Model\VO\Link\LinkId;
use App\Core\Domain\Model\VO\Project\ProjectId;
use Doctrine\ORM\EntityManagerInterface;

final readonly class OrmLinkRepository implements LinkRepository
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function add(Link $link): void
    {
        $this->em->persist($link);
    }

    public function remove(Link $link): void
    {
        $this->em->remove($link);
    }

    public function find(LinkId $id): ?Link
    {
        return $this->em->find(Link::class, $id);
    }

    public function findOneOrFail(LinkId $id): Link
    {
        return $this->find($id) ?? throw new LinkNotFoundException((string) $id);
    }

    /** @return list<Link> */
    public function findByProject(ProjectId $projectId): array
    {
        /** @var list<Link> */
        return $this->em->getRepository(Link::class)->findBy(['projectId' => $projectId]);
    }
}
