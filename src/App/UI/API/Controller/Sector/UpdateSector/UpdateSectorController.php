<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\Sector\UpdateSector;

use App\App\Auth\Domain\Service\SecurityTokenExtractorInterface;
use App\Core\Application\Bus\CommandBus;
use App\Core\Application\Command\Sector\UpdateSector\UpdateSectorCommand;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Sector')]
final class UpdateSectorController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly SecurityTokenExtractorInterface $securityTokenExtractor,
    ) {
    }

    #[Route(path: '/sectors/{id}', methods: ['PATCH'])]
    public function __invoke(string $id, #[MapRequestPayload] UpdateSectorRequest $request): Response
    {
        $this->commandBus->dispatch(new UpdateSectorCommand(
            securityToken: ($this->securityTokenExtractor)(),
            id: $id,
            name: $request->name,
        ));

        return new Response(status: Response::HTTP_OK);
    }
}
