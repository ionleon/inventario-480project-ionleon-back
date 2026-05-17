<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\Link\CreateLink;

use App\App\Auth\Domain\Service\SecurityTokenExtractorInterface;
use App\Core\Application\Bus\CommandBus;
use App\Core\Application\Command\Link\CreateLink\CreateLinkCommand;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Link')]
final class CreateLinkController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly SecurityTokenExtractorInterface $securityTokenExtractor,
    ) {
    }

    #[Route(path: '/links', methods: ['POST'])]
    public function __invoke(#[MapRequestPayload] CreateLinkRequest $request): Response
    {
        $this->commandBus->dispatch(new CreateLinkCommand(
            securityToken: ($this->securityTokenExtractor)(),
            id: $request->id,
            projectId: $request->projectId,
            url: $request->url,
            label: $request->label,
        ));

        return new Response(status: Response::HTTP_CREATED);
    }
}
