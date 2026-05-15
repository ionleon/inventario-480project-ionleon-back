<?php

namespace App\ClientManagement\Infrastructure\Http;

use App\ClientManagement\Application\MarkContactAsMain\MarkContactAsMainCommand;
use App\ClientManagement\Application\MarkContactAsMain\MarkContactAsMainHandler;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[OA\Tag(name: 'Client Management')]
// MIGRATED: route disabled, see src/App/UI/API/Controller/Contact/
// #[Route('/clients/{id}/contacts/{contactId}/main', name: 'app_client_contact_set_main', methods: ['PATCH'])]
#[IsGranted('ROLE_ADMIN')]
final class MarkContactAsMainController extends AbstractController
{
    public function __construct(
        private readonly MarkContactAsMainHandler $handler,
    ) {}

    public function __invoke(string $contactId): JsonResponse
    {
        try {
            $this->handler->handle(new MarkContactAsMainCommand($contactId));

            return $this->json(['message' => 'Contact marked as main successfully.'], 200);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }
}
