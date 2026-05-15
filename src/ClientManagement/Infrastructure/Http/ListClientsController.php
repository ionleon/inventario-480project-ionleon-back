<?php

namespace App\ClientManagement\Infrastructure\Http;

use App\ClientManagement\Application\ListClients\ListClientsHandler;
use App\ClientManagement\Application\ListClients\ListClientsQuery;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[OA\Tag(name: 'Client Management')]
#[Route('/clients', name: 'app_client_index', methods: ['GET'])]
#[IsGranted('ROLE_ADMIN')]
final class ListClientsController extends AbstractController
{
    public function __construct(
        private readonly ListClientsHandler $handler,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $query = new ListClientsQuery(
            term:     $request->query->get('term'),
            isActive: $request->query->has('is_active')
                ? $request->query->getBoolean('is_active')
                : null,
            page:     $request->query->getInt('page', 1),
            limit:    $request->query->getInt('limit', 10),
        );

        $clients = $this->handler->handle($query);

        return $this->json($clients, 200, [], ['groups' => ['client:read']]);
    }
}
