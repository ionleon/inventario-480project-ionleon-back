<?php

namespace App\ProjectManagement\Application\Developments;

use App\ProjectManagement\Application\Developments\Link\LinkService;
use App\ProjectManagement\Domain\Developments\Development;
use App\ProjectManagement\Domain\Developments\DevelopmentRepositoryInterface;
use App\ProjectManagement\Domain\Developments\Technology\TechnologyRepositoryInterface;
use App\ProjectManagement\Domain\Project\Project;
use App\ProjectManagement\Infrastructure\Developments\DoctrineDevelopmentRepository;
use App\Repository\TechnologyRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Uid\Uuid;

class DevelopmentService
{
    public function __construct(
        private DevelopmentRepositoryInterface         $devRepository,
        private TechnologyRepositoryInterface          $technologyRepository,
        private LinkService                            $linkService
    ) {}

    public function create(Project $project, array $data): Development
    {
        if (!isset($data['id'], $data['technology_id'], $data['name'])) {
            throw new \InvalidArgumentException('Mandatory fields missing (id, technology_id, name).');
        }

        $development = new Development();
        $development->setId(Uuid::fromString($data['id']));
        $development->setProject($project);

        return $this->update($development, $data);
    }

    public function update(Development $development, array $data): Development
    {
        $development->setName($data['name'] ?? $development->getName());
        $development->setDescription($data['description'] ?? $development->getDescription());
        $development->setUrlRepository($data['url_repository'] ?? $development->getUrlRepository());


        if (isset($data['technologyId'])) {
            $technology = $this->technologyRepository->findById($data['technology_id']);
            if (!$technology) {
                throw new NotFoundHttpException('Technology not found.');
            }
            $development->setTechnology($technology);
        }

        if (isset($data['links'])) {
            $this->syncLinks($development, $data['links']);
        }


        $this->em->persist($development);
        $this->em->flush();

        return $development;
    }

    public function syncLinks(Development $development, array $linksData): void
    {
        foreach ($development->getLinks() as $existingLink) {
            $this->em->remove($existingLink);
        }

        foreach ($linksData as $linkItem) {
            $this->linkService->create($linkItem, $development, false);
        }
    }

    public function delete(Development $development): void
    {
        $this->em->remove($development);
        $this->em->flush();
    }
}
