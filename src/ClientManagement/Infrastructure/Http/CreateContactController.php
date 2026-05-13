<?php

namespace App\ClientManagement\Infrastructure\Http;

use App\ClientManagement\Application\CreateContact\CreateContactCommand;
use App\ClientManagement\Application\CreateContact\CreateContactHandler;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[OA\Tag(name: 'Client Management')]
#[Route('/clients/{id}/contacts', name: 'app_client_contact_create', methods: ['POST'])]
#[IsGranted('ROLE_ADMIN')]
final class CreateContactController extends AbstractController
{
    public function __construct(
        private readonly CreateContactHandler $handler,
    ) {}

    public function __invoke(string $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $command = new CreateContactCommand(
                clientId:    $id,
                id:          $data['id']           ?? throw new \InvalidArgumentException('Id is required.'),
                fullName:    $data['fullName']     ?? $data['full_name'] ?? throw new \InvalidArgumentException('Full name is required.'),
                email:       $data['email']        ?? throw new \InvalidArgumentException('Email is required.'),
                phoneNumber: $data['phoneNumber']  ?? $data['phone_number'] ?? throw new \InvalidArgumentException('Phone number is required.'),
                note:        $data['note']         ?? null,
                isMain:      $data['is_main']      ?? null,
            );

            $contact = $this->handler->handle($command);

            return $this->json($contact, 201, [], ['groups' => ['contact:read']]);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }
}
