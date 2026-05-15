<?php

namespace App\ProjectManagement\Application\UpdateDevelopment;

use App\ProjectManagement\Domain\Development\Development;
use App\ProjectManagement\Domain\Development\DevelopmentRepositoryInterface;
use App\ProjectManagement\Domain\Development\Link\Link;
use App\ProjectManagement\Domain\Development\Link\LinkRepositoryInterface;
use App\ProjectManagement\Domain\Development\Technology\TechnologyRepositoryInterface;
use App\Shared\Domain\Enum\Enviroment;
use Symfony\Component\Uid\Uuid;

final class UpdateDevelopmentHandler
{
    public function __construct(
        private readonly DevelopmentRepositoryInterface $developmentRepository,
        private readonly TechnologyRepositoryInterface $technologyRepository,
        private readonly LinkRepositoryInterface $linkRepository,
    ) {}

    public function handle(UpdateDevelopmentCommand $command): Development
    {
        $development = $this->developmentRepository->findById($command->developmentId);

        if (!$development) {
            throw new \DomainException('Development not found');
        }

        if ($command->name !== null) {
            $development->setName($command->name);
        }

        if ($command->description !== null) {
            $development->setDescription($command->description);
        }

        if ($command->urlRepository !== null) {
            $development->setUrlRepository($command->urlRepository);
        }

        if ($command->technologyId !== null) {
            $technology = $this->technologyRepository->findById($command->technologyId);

            if (!$technology) {
                throw new \DomainException("Technology with ID {$command->technologyId} not found.");
            }

            $development->setTechnology($technology);
        }

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
