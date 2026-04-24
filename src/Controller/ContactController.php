<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Repository\ContactRepository;
use App\Service\ContactManager;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/contacts', name: 'app_client')]
final class ContactController extends AbstractController
{

    public function __construct(
        private ContactManager $contactManager,
        private contactRepository $contactRepository
    ) { }

    #[Route('', name: 'contact_index', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $clientId = $request->query->get('client_id');
        if ($clientId) {
            $contacts = $this->contactRepository->findBy(['client' => $clientId]);
        } else {
            $contacts = $this->contactRepository->findAll();
        }

        return $this->json($contacts, 200, [], ['groups' => 'client:read']);
    }

    #[Route('', name: 'create_contact', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        try {
            $contact = $this->contactManager->create($data);
            return $this->json($contact, 201, [], ['groups' => 'contact:read']);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/{id}', name: 'app_contact', methods: ['PUT', 'PATCH'])]
    public function update(Contact $contact, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $this->contactManager->save($contact, $data);
            return $this->json($contact, 200, [], ['groups' => 'contact:read']);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Contact $contact): JsonResponse
    {
        try {
            $this->contactManager->delete($contact);
            return $this->json(null, 204);
        } catch (\LogicException $e) {
            return $this->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
