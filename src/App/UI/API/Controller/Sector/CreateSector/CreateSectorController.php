<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\Sector\CreateSector;

use App\App\Auth\Domain\Service\SecurityTokenExtractorInterface;
use App\Core\Application\Bus\CommandBus;
use App\Core\Application\Command\Sector\CreateSector\CreateSectorCommand;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Sector')]
final class CreateSectorController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly SecurityTokenExtractorInterface $securityTokenExtractor,
    ) {
    }

    #[Route(path: '/sectors', methods: ['POST'])]
    public function __invoke(#[MapRequestPayload] CreateSectorRequest $request): Response
    {
        $this->commandBus->dispatch(new CreateSectorCommand(
            securityToken: ($this->securityTokenExtractor)(),
            id: $request->id,
            name: $request->name,
        ));

        return new Response(status: Response::HTTP_CREATED);
    }
}
