<?php

namespace App\Controller;

use App\Service\AuthManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('', name: 'authentication_logic')]
final class AuthController extends AbstractController
{
    public function __construct(
        private readonly AuthManager $authManager
    )
    {}

    #[Route('/logout', name: 'app_logout', methods: ['POST'])]
    public function logout(Request $request): Response
    {
        try {
            $data = json_decode($request->getContent(), true);
            $refreshToken = $data['refresh_token'] ?? null;

            if (!$refreshToken) {
                return $this->json(['error' => 'Refresh token required'], 400);
            }

            $this->authManager->logout($refreshToken);

            return $this->json(['message' => 'Session closed successfully'], 200);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'An error ocurred during logout',
                'details'=> $e->getMessage()
            ], 500);
        }

    }
}
