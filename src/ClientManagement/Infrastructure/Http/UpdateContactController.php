<?php

namespace App\ClientManagement\Infrastructure\Http;

use App\ClientManagement\Application\UpdateContact\UpdateContactCommand;
use App\ClientManagement\Application\UpdateContact\UpdateContactHandler;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[OA\Tag(name: 'Client Management')]
#[Route('/clients/{id}/contacts/{contactId}', name: 'app_client_contact_update', methods: ['PUT', 'PATCH'])]
#[IsGranted('ROLE_ADMIN')]
final class UpdateContactController extends AbstractController
{
    public function __construct(
        private readonly UpdateContactHandler $handler,
    ) {}

    public function __invoke(string $contactId, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $command = new UpdateContactCommand(
                contactId:   $contactId,
                fullName:    $data['fullName']     ?? $data['full_name'] ?? null,
                email:       $data['email']        ?? null,
                phoneNumber: $data['phoneNumber']  ?? $data['phone_number'] ?? null,
                note:        $data['note']         ?? null,
                isMain:      $data['is_main']      ?? null,
            );

            $this->handler->handle($command);

            return $this->json(['message' => 'Contact updated'], 200, [], ['groups' => ['contact:read']]);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        } catch (\LogicException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }
}
