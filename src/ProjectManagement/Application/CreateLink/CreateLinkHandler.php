<?php

namespace App\ProjectManagement\Application\CreateLink;

use App\ProjectManagement\Domain\Development\DevelopmentRepositoryInterface;
use App\ProjectManagement\Domain\Development\Link\Link;
use App\ProjectManagement\Domain\Development\Link\LinkRepositoryInterface;
use App\Shared\Domain\Enum\Enviroment;
use Symfony\Component\Uid\Uuid;

final class CreateLinkHandler
{
    public function __construct(
        private readonly LinkRepositoryInterface $linkRepository,
        private readonly DevelopmentRepositoryInterface $developmentRepository,
    ) {}

    public function handle(CreateLinkCommand $command): Link
    {
        $development = $this->developmentRepository->findById($command->developmentId);

        if (!$development) {
            throw new \DomainException("Development with ID {$command->developmentId} not found.");
        }

        $link = new Link();
        $link->setId(Uuid::fromString($command->id));
        $link->setUrl($command->url);
        $link->setEnviroment(Enviroment::from($command->enviroment));
        $link->setDevelopment($development);

        $this->linkRepository->save($link, true);

        return $link;
    }
}
