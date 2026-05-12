<?php

namespace App\ClientManagement\Infrastructure\Contact;

use App\ClientManagement\Application\Contact\ContactService;
use App\ClientManagement\Domain\Client\Client;
use App\ClientManagement\Domain\Contact\Contact;
use App\ClientManagement\Domain\Contact\ContactRepositoryInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/clients/{id}/contacts', name: 'app_client')]
final class ContactController extends AbstractController
{

    public function __construct(
        private readonly ContactService             $contactService,
        private readonly ContactRepositoryInterface $contactRepository
    ) { }

    #[Route('', name: '_contact_index', methods: ['GET'])]
    public function index(Client $client, Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        $contacts = $this->contactRepository->findByClientPaginated($client, $page, $limit);
        $contacts = $this->contactRepository->findByClient($client);


        return $this->json($contacts, 200, [], ['groups' => 'client:read']);
    }

    #[Route('', name: '_contact_create', methods: ['POST'])]
    public function create(Client $client, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        try {
            $contact = $this->contactService->create($data, $client);
            return $this->json($contact, 201, [], ['groups' => 'contact:read']);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/{contactId}', name: '_contact_update', methods: ['PUT', 'PATCH'])]
    public function update(
        #[MapEntity(mapping: ['contactId' => 'id'])] Contact $contact,
        Request $request
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $this->contactService->update($contact, $data);
            return $this->json(['message' => 'Contact updated'], 200, [], ['groups' => 'contact:read']);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/{contactId}', name: '_contact_delete', methods: ['DELETE'])]
    public function delete(
        #[MapEntity(mapping: ['contactId' => 'id'])] Contact $contact
    ): JsonResponse
    {
        try {
            $this->contactService->delete($contact);
            return $this->json(null, 204);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/{contactId}', name: '_contact_set_main', methods: ['PATCH'])]
    public function setMain(
        #[MapEntity(mapping: ['contactId' => 'id'])] Contact $contact
    ): JsonResponse
    {
        try {
            $this->contactService->markAsMain($contact);
            return $this->json([
                'message' => 'Contact marked as main successfully.'
            ], 200);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }
}
