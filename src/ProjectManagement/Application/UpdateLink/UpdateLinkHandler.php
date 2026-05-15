<?php

namespace App\ProjectManagement\Application\UpdateLink;

use App\ProjectManagement\Domain\Development\DevelopmentRepositoryInterface;
use App\ProjectManagement\Domain\Development\Link\Link;
use App\ProjectManagement\Domain\Development\Link\LinkRepositoryInterface;
use App\Shared\Domain\Enum\Enviroment;

final class UpdateLinkHandler
{
    public function __construct(
        private readonly LinkRepositoryInterface $linkRepository,
        private readonly DevelopmentRepositoryInterface $developmentRepository,
    ) {}

    public function handle(UpdateLinkCommand $command): Link
    {
        $link = $this->linkRepository->findById($command->linkId);

        if (!$link) {
            throw new \DomainException('Link not found');
        }

        if ($command->url !== null) {
            $link->setUrl($command->url);
        }

        if ($command->enviroment !== null) {
            $link->setEnviroment(Enviroment::from($command->enviroment));
        }

        if ($command->developmentId !== null) {
            $development = $this->developmentRepository->findById($command->developmentId);

            if (!$development) {
                throw new \DomainException("Development with ID {$command->developmentId} not found.");
            }

            $link->setDevelopment($development);
        }

        $this->linkRepository->save($link, true);

        return $link;
    }
}
