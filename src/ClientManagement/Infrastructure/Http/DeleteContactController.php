<?php

namespace App\ClientManagement\Infrastructure\Http;

use App\ClientManagement\Application\DeleteContact\DeleteContactCommand;
use App\ClientManagement\Application\DeleteContact\DeleteContactHandler;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[OA\Tag(name: 'Client Management')]
// MIGRATED: route disabled, see src/App/UI/API/Controller/Contact/
// #[Route('/clients/{id}/contacts/{contactId}', name: 'app_client_contact_delete', methods: ['DELETE'])]
#[IsGranted('ROLE_ADMIN')]
final class DeleteContactController extends AbstractController
{
    public function __construct(
        private readonly DeleteContactHandler $handler,
    ) {}

    public function __invoke(string $contactId): JsonResponse
    {
        try {
            $this->handler->handle(new DeleteContactCommand($contactId));

            return $this->json(null, 204);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        } catch (\LogicException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }
}
