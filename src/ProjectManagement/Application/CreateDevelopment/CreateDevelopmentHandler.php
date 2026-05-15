<?php

namespace App\ProjectManagement\Application\CreateDevelopment;

use App\ProjectManagement\Domain\Development\Development;
use App\ProjectManagement\Domain\Development\DevelopmentRepositoryInterface;
use App\ProjectManagement\Domain\Development\Link\Link;
use App\ProjectManagement\Domain\Development\Link\LinkRepositoryInterface;
use App\ProjectManagement\Domain\Development\Technology\TechnologyRepositoryInterface;
use App\ProjectManagement\Domain\Project\ProjectRepositoryInterface;
use App\Shared\Domain\Enum\Enviroment;
use Symfony\Component\Uid\Uuid;

final class CreateDevelopmentHandler
{
    public function __construct(
        private readonly ProjectRepositoryInterface $projectRepository,
        private readonly DevelopmentRepositoryInterface $developmentRepository,
        private readonly TechnologyRepositoryInterface $technologyRepository,
        private readonly LinkRepositoryInterface $linkRepository,
    ) {}

    public function handle(CreateDevelopmentCommand $command): Development
    {
        $project = $this->projectRepository->findById($command->projectId);

        if (!$project) {
            throw new \DomainException('Project not found');
        }

        $technology = $this->technologyRepository->findById($command->technologyId);

        if (!$technology) {
            throw new \DomainException("Technology with ID {$command->technologyId} not found.");
        }

        $development = new Development();
        $development->setId(Uuid::fromString($command->id));
        $development->setProject($project);
        $development->setTechnology($technology);
        $development->setName($command->name);
        $development->setDescription($command->description);
        $development->setUrlRepository($command->urlRepository);

        $this->developmentRepository->save($development);

        if ($command->links !== null) {
            $this->syncLinks($development, $command->links);
        }

        return $development;
    }

    private function syncLinks(Development $development, array $linksData): void
    {
        foreach ($development->getLinks() as $existingLink) {
            $this->linkRepository->delete($existingLink);
        }

        foreach ($linksData as $linkItem) {
            if (!isset($linkItem['id'], $linkItem['url'], $linkItem['enviroment'])) {
                throw new \InvalidArgumentException('Missing mandatory fields (id, url, enviroment) in link.');
            }

            $link = new Link();
            $link->setId(Uuid::fromString($linkItem['id']));
            $link->setDevelopment($development);
            $link->setUrl($linkItem['url']);
            $link->setEnviroment(Enviroment::from($linkItem['enviroment']));

            $this->linkRepository->save($link, true);
        }
    }
}
