<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\Link\UpdateLink;

use App\App\Auth\Domain\Service\SecurityTokenExtractorInterface;
use App\Core\Application\Bus\CommandBus;
use App\Core\Application\Command\Link\UpdateLink\UpdateLinkCommand;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Link')]
final class UpdateLinkController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly SecurityTokenExtractorInterface $securityTokenExtractor,
    ) {}

    #[Route(path: '/links/{id}', methods: ['PUT'])]
    public function __invoke(string $id, #[MapRequestPayload] UpdateLinkRequest $request): Response
    {
        $this->commandBus->dispatch(new UpdateLinkCommand(
            securityToken: ($this->securityTokenExtractor)(),
            id: $id,
            url: $request->url,
            label: $request->label,
        ));

        return new Response(status: Response::HTTP_OK);
    }
}
