<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\Client\CreateClient;

use App\App\Auth\Domain\Service\SecurityTokenExtractorInterface;
use App\Core\Application\Bus\CommandBus;
use App\Core\Application\Command\Client\CreateClient\CreateClientCommand;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Client')]
final class CreateClientController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly SecurityTokenExtractorInterface $securityTokenExtractor,
    ) {
    }

    #[Route(path: '/clients', methods: ['POST'])]
    public function __invoke(#[MapRequestPayload] CreateClientRequest $request): Response
    {
        $this->commandBus->dispatch(new CreateClientCommand(
            securityToken: ($this->securityTokenExtractor)(),
            id: $request->id,
            name: $request->name,
            sectorId: $request->sectorId,
        ));

        return new Response(status: Response::HTTP_CREATED);
    }
}
