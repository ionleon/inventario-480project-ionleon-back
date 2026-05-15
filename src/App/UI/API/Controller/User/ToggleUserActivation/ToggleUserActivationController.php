<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\User\ToggleUserActivation;

use App\App\Auth\Domain\Service\SecurityTokenExtractorInterface;
use App\Core\Application\Bus\CommandBus;
use App\Core\Application\Command\User\ToggleUserActivation\ToggleUserActivationCommand;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'User')]
final class ToggleUserActivationController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly SecurityTokenExtractorInterface $securityTokenExtractor,
    ) {}

    #[Route(path: '/users/{id}/toggle-activation', methods: ['PATCH'])]
    public function __invoke(string $id): Response
    {
        $this->commandBus->dispatch(new ToggleUserActivationCommand(
            securityToken: ($this->securityTokenExtractor)(),
            id: $id,
        ));

        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}
