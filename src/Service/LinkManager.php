<?php

namespace App\Service;

use App\Entity\Development;
use App\Entity\Link;
use App\Enum\Enviroment;
use App\Repository\LinkRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Uid\Uuid;

class LinkManager
{
    public function __construct(
      private EntityManagerInterface $em,
      private LinkRepository $repository
    ) {}

    public function create(array $data): Link
    {
        if (!isset($data['id'], $data['developmentId'], $data['url'], $data['enviroment'])){
            throw new \InvalidArgumentException('Missing mandatory fields (id, developmentId, url, enviroment)');
        }

        $link = new Link();

        try{
            $link->setId(Uuid::fromString($data['id']));
        } catch (InvalidArgumentException $e) {
            throw new \InvalidArgumentException('Invalid UUID format.');
        }

        $development = $this->em->getRepository(Development::class)->find($data['developmentId']);

        if (!$development){
            throw new NotFoundHttpException('Development not found.');
        }

        $link->setDevelopment($development);

        return $this->save($link, $data);

    }

    public function save(Link $link, array $data): Link
    {
        if (isset($data['enviroment'])) {
            $link->setEnviroment(Enviroment::from($data['enviroment']));
        }

        $link->setUrl($data['url'] ?? $link->getUrl());

        $this->em->persist($link);
        $this->em->flush();

        return $link;
    }

    public function delete(Link $link): void
    {
        $this->em->remove($link);
        $this->em->flush();
    }
}
