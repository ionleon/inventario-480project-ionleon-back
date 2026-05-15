<?php

namespace App\ClientManagement\Infrastructure\Http;

use App\ClientManagement\Application\UpdateClient\UpdateClientCommand;
use App\ClientManagement\Application\UpdateClient\UpdateClientHandler;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[OA\Tag(name: 'Client Management')]
#[Route('/clients/{id}', name: 'app_client_update', methods: ['PUT'])]
#[IsGranted('ROLE_ADMIN')]
final class UpdateClientController extends AbstractController
{
    public function __construct(
        private readonly UpdateClientHandler $handler,
    ) {}

    public function __invoke(string $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $command = new UpdateClientCommand(
                clientId: $id,
                name:     $data['name']      ?? null,
                isActive: $data['is_active'] ?? null,
                sectorId: $data['sector_id'] ?? null,
            );

            $this->handler->handle($command);

            return $this->json(['message' => 'Client updated'], 200, [], ['groups' => ['client:read']]);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 409);
        } catch (\LogicException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }
}
