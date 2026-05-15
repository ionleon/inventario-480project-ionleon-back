<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\Contact\MarkContactAsMain;

use App\App\Auth\Domain\Service\SecurityTokenExtractorInterface;
use App\Core\Application\Bus\CommandBus;
use App\Core\Application\Command\Contact\MarkContactAsMain\MarkContactAsMainCommand;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Contact')]
final class MarkContactAsMainController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly SecurityTokenExtractorInterface $securityTokenExtractor,
    ) {}

    #[Route(path: '/clients/{clientId}/contacts/{id}/main', methods: ['PATCH'])]
    public function __invoke(string $id): Response
    {
        $this->commandBus->dispatch(new MarkContactAsMainCommand(
            securityToken: ($this->securityTokenExtractor)(),
            id: $id,
        ));

        return new Response(status: Response::HTTP_OK);
    }
}
