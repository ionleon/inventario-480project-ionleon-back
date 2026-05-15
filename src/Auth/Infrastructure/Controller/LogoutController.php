<?php

namespace App\Auth\Infrastructure\Controller;

use App\Auth\Application\Logout\LogoutCommand;
use App\Auth\Application\Logout\LogoutHandler;
use App\Auth\Domain\Service\TokenPayloadExtractorInterface;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// MIGRATED: route disabled, see src/App/UI/API/Controller/Auth/Logout/LogoutController.php
#[OA\Tag(name: 'Authentication')]
// #[Route('/logout', name: 'app_logout', methods: ['POST'])]
final class LogoutController extends AbstractController
{
    public function __construct(
        private readonly LogoutHandler $handler,
        private readonly TokenPayloadExtractorInterface $tokenPayloadExtractor,
        private readonly LoggerInterface $logger,
    ) {}

    public function __invoke(Request $request): Response
    {
        $refreshToken = $request->getPayload()->get('refresh_token');

        if (!$refreshToken) {
            return $this->json(['error' => 'Refresh token required'], 400);
        }

        $payload = $this->tokenPayloadExtractor->extractFromCurrentRequest();

        if (!$payload || !isset($payload['jti'], $payload['ttl'])) {
            return $this->json(['error' => 'Invalid or missing JWT'], 401);
        }

        try {
            $command = new LogoutCommand(
                refreshToken: $refreshToken,
                jti: $payload['jti'],
                ttl: $payload['ttl'],
            );

            $this->handler->handle($command);

            return $this->json(['message' => 'Session closed successfully'], 204);
        } catch (\Exception $e) {
            $this->logger->error('Logout Failed', ['exception' => $e]);
            return $this->json(['error' => 'An error occurred during logout'], 500);
        }
    }
}
