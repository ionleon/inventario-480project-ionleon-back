<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\Auth\Logout;

use App\App\Auth\Domain\Service\SecurityTokenExtractorInterface;
use App\Core\Application\Bus\CommandBus;
use App\Core\Application\Command\Auth\Logout\LogoutCommand;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Authentication')]
final class LogoutController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly SecurityTokenExtractorInterface $securityTokenExtractor,
    ) {
    }

    #[Route(path: '/logout', methods: ['POST'])]
    public function __invoke(Request $request): Response
    {
        $refreshToken = $request->getPayload()->getString('refresh_token');

        if ($refreshToken === '') {
            return new Response(
                content: json_encode(['error' => 'Refresh token required']),
                status: Response::HTTP_BAD_REQUEST,
                headers: ['Content-Type' => 'application/json'],
            );
        }

        $this->commandBus->dispatch(new LogoutCommand(
            securityToken: ($this->securityTokenExtractor)(),
            refreshToken: $refreshToken,
        ));

        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}
