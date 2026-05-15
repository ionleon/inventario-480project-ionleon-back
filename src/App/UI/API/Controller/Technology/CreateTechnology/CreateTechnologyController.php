<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\Technology\CreateTechnology;

use App\App\Auth\Domain\Service\SecurityTokenExtractorInterface;
use App\Core\Application\Bus\CommandBus;
use App\Core\Application\Command\Technology\CreateTechnology\CreateTechnologyCommand;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Technology')]
final class CreateTechnologyController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly SecurityTokenExtractorInterface $securityTokenExtractor,
    ) {}

    #[Route(path: '/technologies', methods: ['POST'])]
    public function __invoke(#[MapRequestPayload] CreateTechnologyRequest $request): Response
    {
        $this->commandBus->dispatch(new CreateTechnologyCommand(
            securityToken: ($this->securityTokenExtractor)(),
            id: $request->id,
            name: $request->name,
        ));

        return new Response(status: Response::HTTP_CREATED);
    }
}
