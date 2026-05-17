<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\Sector\DeleteSector;

use App\App\Auth\Domain\Service\SecurityTokenExtractorInterface;
use App\Core\Application\Bus\CommandBus;
use App\Core\Application\Command\Sector\DeleteSector\DeleteSectorCommand;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Sector')]
final class DeleteSectorController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly SecurityTokenExtractorInterface $securityTokenExtractor,
    ) {
    }

    #[Route(path: '/sectors/{id}', methods: ['DELETE'])]
    public function __invoke(string $id): Response
    {
        $this->commandBus->dispatch(new DeleteSectorCommand(
            securityToken: ($this->securityTokenExtractor)(),
            id: $id,
        ));

        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}
