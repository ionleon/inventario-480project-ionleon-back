<?php

namespace App\ClientManagement\Infrastructure\Http;

use App\ClientManagement\Application\GetClient\GetClientHandler;
use App\ClientManagement\Application\GetClient\GetClientQuery;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[OA\Tag(name: 'Client Management')]
// MIGRATED: route disabled, see src/App/UI/API/Controller/Client/
// #[Route('/clients/{id}', name: 'app_client_show', methods: ['GET'])]
#[IsGranted('ROLE_ADMIN')]
final class GetClientController extends AbstractController
{
    public function __construct(
        private readonly GetClientHandler $handler,
    ) {}

    public function __invoke(string $id): JsonResponse
    {
        try {
            $client = $this->handler->handle(new GetClientQuery($id));

            return $this->json($client, 200, [], ['groups' => ['client:read']]);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }
}
