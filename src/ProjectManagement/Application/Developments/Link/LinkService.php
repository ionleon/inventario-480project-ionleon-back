<?php

namespace App\ProjectManagement\Application\Developments\Link;

use App\Enum\Enviroment;
use App\ProjectManagement\Domain\Developments\Development;
use App\ProjectManagement\Domain\Developments\DevelopmentRepositoryInterface;
use App\ProjectManagement\Domain\Developments\Link\Link;
use App\ProjectManagement\Domain\Developments\Link\LinkRepositoryInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Uid\Uuid;

class LinkService
{
    public function __construct(
      private readonly LinkRepositoryInterface        $repository,
      private readonly DevelopmentRepositoryInterface $developmentRepository
    ) {}

    /**
     * @throws \Exception
     */
    public function create(array $data, ?Development $development = null, bool $flush = true): Link
    {
        if (!isset($data['id'], $data['url'], $data['enviroment'])){
            throw new \InvalidArgumentException('Missing mandatory fields (id, developmentId, url, enviroment)');
        }

        $link = new Link();


        try {
            $link->setId(Uuid::fromString($data['id']));
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException('Invalid UUID format.');
        }


        if (!$development && isset($data['development_id'])) {
            $development = $this->developmentRepository->find($data['development_id']);
        }

        if (!$development){
            throw new \Exception('Link must be assigned to a development.');
        }

        $link->setDevelopment($development);

        return $this->save($link, $data, $flush);

    }

    public function save(Link $link, array $data, bool $flush = true): Link
    {
        if (isset($data['enviroment'])) {
            $link->setEnviroment(Enviroment::from($data['enviroment']));
        }

        $link->setUrl($data['url'] ?? $link->getUrl());

        $this->repository->save($link, $flush);
        return $link;
    }

    public function delete(Link $link): void
    {
        $this->repository->delete($link);
    }
}
