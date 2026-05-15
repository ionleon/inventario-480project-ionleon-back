<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\Auth\ForceLogout;

use App\App\Auth\Domain\Service\SecurityTokenExtractorInterface;
use App\Core\Application\Bus\CommandBus;
use App\Core\Application\Command\Auth\ForceLogout\ForceLogoutCommand;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Authentication')]
final class ForceLogoutController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly SecurityTokenExtractorInterface $securityTokenExtractor,
    ) {}

    #[Route(path: '/users/{identifier}/force-logout', methods: ['POST'])]
    public function __invoke(string $identifier): Response
    {
        $this->commandBus->dispatch(new ForceLogoutCommand(
            securityToken: ($this->securityTokenExtractor)(),
            targetUserIdentifier: $identifier,
        ));

        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}
