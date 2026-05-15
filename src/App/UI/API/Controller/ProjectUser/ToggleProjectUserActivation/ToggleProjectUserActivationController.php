<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\ProjectUser\ToggleProjectUserActivation;

use App\App\Auth\Domain\Service\SecurityTokenExtractorInterface;
use App\Core\Application\Bus\CommandBus;
use App\Core\Application\Command\ProjectUser\ToggleProjectUserActivation\ToggleProjectUserActivationCommand;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'ProjectUser')]
final class ToggleProjectUserActivationController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly SecurityTokenExtractorInterface $securityTokenExtractor,
    ) {}

    #[Route(path: '/project-users/{id}/toggle-activation', methods: ['POST'])]
    public function __invoke(string $id): Response
    {
        $this->commandBus->dispatch(new ToggleProjectUserActivationCommand(
            securityToken: ($this->securityTokenExtractor)(),
            id: $id,
        ));

        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}
