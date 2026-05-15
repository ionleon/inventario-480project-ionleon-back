<?php

namespace App\ClientManagement\Infrastructure\Http;

use App\ClientManagement\Application\ListContacts\ListContactsHandler;
use App\ClientManagement\Application\ListContacts\ListContactsQuery;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[OA\Tag(name: 'Client Management')]
// MIGRATED: route disabled, see src/App/UI/API/Controller/Contact/
// #[Route('/clients/{id}/contacts', name: 'app_client_contact_index', methods: ['GET'])]
#[IsGranted('ROLE_ADMIN')]
final class ListContactsController extends AbstractController
{
    public function __construct(
        private readonly ListContactsHandler $handler,
    ) {}

    public function __invoke(string $id, Request $request): JsonResponse
    {
        try {
            $query = new ListContactsQuery(
                clientId: $id,
                page:     $request->query->getInt('page', 1),
                limit:    $request->query->getInt('limit', 10),
            );

            $contacts = $this->handler->handle($query);

            return $this->json($contacts, 200, [], ['groups' => ['contact:read']]);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }
}
