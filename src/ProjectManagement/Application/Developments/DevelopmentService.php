<?php

namespace App\ProjectManagement\Application\Developments;

use App\ProjectManagement\Application\Developments\Link\LinkService;
use App\ProjectManagement\Domain\Developments\Development;
use App\ProjectManagement\Domain\Developments\DevelopmentRepositoryInterface;
use App\ProjectManagement\Domain\Developments\Link\LinkRepositoryInterface;
use App\ProjectManagement\Domain\Developments\Technology\TechnologyRepositoryInterface;
use App\ProjectManagement\Domain\Project\Project;
use App\ProjectManagement\Infrastructure\Developments\Technology\DoctrineTechnologyRepository;
use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Uid\Uuid;


class DevelopmentService

{
    public function __construct(
        private DevelopmentRepositoryInterface $devRepository,
        private TechnologyRepositoryInterface  $technologyRepository,
        private LinkRepositoryInterface        $linkRepository,
        private LinkService                    $linkManager
    ) {}

    public function create(Project $project, array $data): Development

    {

        if (!isset($data['id'], $data['technology_id'], $data['name'])) {

            throw new \InvalidArgumentException('Faltan campos obligatorios (id, technologyId, name).');

        }

        $development = new Development();
        $development->setId(Uuid::fromString($data['id']));
        $development->setProject($project);


        return $this->save($development, $data);

    }


    /**
     * @throws Exception
     */
    public function save(Development $development, array $data): Development

    {
        $development->setName($data['name'] ?? $development->getName());
        $development->setDescription($data['description'] ?? $development->getDescription());
        $development->setUrlRepository($data['url_repository'] ?? $development->getUrlRepository());

        if (isset($data['technology_id'])) {
            $technology = $this->technologyRepository->find($data['technology_id']);
            if (!$technology) {
                throw new Exception("Technology with ID {$data['technology_id']} not found.");
            }
            $development->setTechnology($technology);
        }


        if (isset($data['links'])) {
            $this->syncLinks($development, $data['links']);
        }

        $this->devRepository->save($development);

        return $development;
    }


    public function syncLinks(Development $development, array $linksData): void
    {
        foreach ($development->getLinks() as $existingLink) {
            $this->linkRepository->delete($existingLink);
        }

        foreach ($linksData as $linkItem) {
            $this->linkManager->create($linkItem, $development, false);
        }

    }


    public function delete(Development $development): void
    {
        $this->devRepository->delete($development);
    }

}
