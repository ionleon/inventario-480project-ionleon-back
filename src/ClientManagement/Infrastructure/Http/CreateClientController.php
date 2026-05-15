<?php

namespace App\ClientManagement\Infrastructure\Http;

use App\ClientManagement\Application\CreateClient\CreateClientCommand;
use App\ClientManagement\Application\CreateClient\CreateClientHandler;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[OA\Tag(name: 'Client Management')]
// MIGRATED: route disabled, see src/App/UI/API/Controller/Client/
// #[Route('/clients', name: 'app_client_create', methods: ['POST'])]
#[IsGranted('ROLE_ADMIN')]
final class CreateClientController extends AbstractController
{
    public function __construct(
        private readonly CreateClientHandler $handler,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $command = new CreateClientCommand(
                id:       $data['id']        ?? throw new \InvalidArgumentException('Id is required.'),
                name:     $data['name']      ?? throw new \InvalidArgumentException('Name is required.'),
                sectorId: $data['sector_id'] ?? throw new \InvalidArgumentException('Sector is required.'),
            );

            $this->handler->handle($command);

            return $this->json(['message' => 'Client created'], 201);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 409);
        }
    }
}
