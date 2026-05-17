<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\Client\ToggleClientActivation;

use App\App\Auth\Domain\Service\SecurityTokenExtractorInterface;
use App\Core\Application\Bus\CommandBus;
use App\Core\Application\Command\Client\ToggleClientActivation\ToggleClientActivationCommand;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Client')]
final class ToggleClientActivationController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly SecurityTokenExtractorInterface $securityTokenExtractor,
    ) {
    }

    #[Route(path: '/clients/{id}', methods: ['PATCH'])]
    public function __invoke(string $id): Response
    {
        $this->commandBus->dispatch(new ToggleClientActivationCommand(
            securityToken: ($this->securityTokenExtractor)(),
            id: $id,
        ));

        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}
