<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\Client\UpdateClient;

use App\App\Auth\Domain\Service\SecurityTokenExtractorInterface;
use App\Core\Application\Bus\CommandBus;
use App\Core\Application\Command\Client\UpdateClient\UpdateClientCommand;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Client')]
final class UpdateClientController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly SecurityTokenExtractorInterface $securityTokenExtractor,
    ) {}

    #[Route(path: '/clients/{id}', methods: ['PATCH'])]
    public function __invoke(string $id, #[MapRequestPayload] UpdateClientRequest $request): Response
    {
        $this->commandBus->dispatch(new UpdateClientCommand(
            securityToken: ($this->securityTokenExtractor)(),
            id: $id,
            name: $request->name,
            sectorId: $request->sectorId,
        ));

        return new Response(status: Response::HTTP_OK);
    }
}
