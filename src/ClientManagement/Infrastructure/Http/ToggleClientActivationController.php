<?php

namespace App\ClientManagement\Infrastructure\Http;

use App\ClientManagement\Application\ToggleClientActivation\ToggleClientActivationCommand;
use App\ClientManagement\Application\ToggleClientActivation\ToggleClientActivationHandler;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[OA\Tag(name: 'Client Management')]
#[Route('/clients/{id}', name: 'app_client_deactivate', methods: ['PATCH'])]
#[IsGranted('ROLE_ADMIN')]
final class ToggleClientActivationController extends AbstractController
{
    public function __construct(
        private readonly ToggleClientActivationHandler $handler,
    ) {}

    public function __invoke(string $id): JsonResponse
    {
        try {
            $this->handler->handle(new ToggleClientActivationCommand($id));

            return $this->json(['message' => 'Client updated'], 200);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }
}
