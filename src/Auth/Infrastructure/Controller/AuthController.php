<?php

namespace App\Auth\Infrastructure\Controller;

use App\Auth\Application\AuthService;
use App\Auth\Domain\Service\TokenPayloadExtractorInterface;
use App\Auth\Infrastructure\Service\LexikPayloadExtractor;
use Doctrine\Common\Annotations\TokenParser;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('', name: 'authentication_logic')]
final class AuthController extends AbstractController
{
    public function __construct(
        private readonly AuthService                    $authManager,
        private readonly TokenPayloadExtractorInterface $tokenPayloadExtractor,
        private readonly LoggerInterface                $logger,
    )
    {}

    #[Route('/logout', name: 'app_logout', methods: ['POST'])]
    public function logout(Request $request): Response
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
            $this->authManager->logout($refreshToken, $payload['jti'], $payload['ttl']);
        } catch (\Exception $e) {
            $this->logger->error('Logout Failed', ['exception' => $e]);
            return $this->json(['error' => 'An error ocurred during logout'], 500);
        }
        return $this->json(['message' => 'Session closed successfully'], 204);
    }
}
