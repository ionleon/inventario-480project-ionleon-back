<?php

namespace App\ClientManagement\Infrastructure\Client;

use App\ClientManagement\Application\ClientService;
use App\ClientManagement\Application\Contact\ContactService;
use App\ClientManagement\Domain\Client\Client;
use App\ClientManagement\Domain\Client\ClientFilters;
use App\ClientManagement\Domain\Client\ClientRepositoryInterface;
use App\ClientManagement\Domain\Contact\ContactRepositoryInterface;
use App\ProjectManagement\Domain\Project\ProjectRepositoryInterface;
use Exception;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/clients', name: 'app_client')]
#[OA\Tag(name: 'Projects')]
final class ClientController extends AbstractController
{


    public function __construct(
        private readonly ClientService              $clientService,
        private readonly ClientRepositoryInterface  $clientRepository,
        private readonly ProjectRepositoryInterface $projectRepository,
        private readonly ContactRepositoryInterface $contactRepository,
        private readonly ContactService             $contactService
    ) {}

    /**
     * @throws Exception
     */
    #[Route('', name: 'index', methods:['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function index(Request $request): JsonResponse
    {

        $filters = new ClientFilters(
            term: $request->query->get('term'),
            isActive: $request->query->get('is_active')? $request->query->getBoolean('is_active') : null
        );

        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        $clients = $this->clientRepository->findWithSectorsPaginated($filters, $page, $limit);

        return $this->json($clients, 200, [], ['groups' => ['client:read']]);
    }

    #[Route('/{id}', name:'show', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function show(Client $client): JsonResponse
    {

        return $this->json($client, 200, [], ['groups' => ['client:read']]);
    }

    /**
     * @throws Exception
     */
    #[Route('/{id}/projects', name:'show_projects', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function showProjects(Client $client, Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);


        $projects = $this->projectRepository->findByClientPaginated($client->getId(), $page, $limit);

        return $this->json($projects, 200, [], ['groups' => ['project:read']]);
    }

    /**
     * @throws Exception
     */
    #[Route('/{id}/contacts', name:'client_show_contacts', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function showContacts(Client $client, Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        $contacts = $this->contactRepository->findByClientPaginated($client->getId(), $page, $limit);

        return $this->json($contacts, 200, [], ['groups' => ['contact:read']]);
    }

    #[Route('/{id}/contacts', name:'create_contacts', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function createContacts(Client $client, Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $this->contactService->create($data, $client);

            return $this->json(['message' => 'Contact created'], 201, [], ['groups' => ['contact:read']]);
        } catch (Exception $e){
            return $this->json(['error' => $e->getMessage()], 400);
        }

    }

    #[Route('', name: 'create_client', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $client = $this->clientService->create($data);

            return $this->json(['message' => 'Client created'], 201);
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/{id}', name:'client_update', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function update(Client $client, Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $this->clientService->update($client, $data);

            return $this->json(['message' => 'Client updated'], 201, [], ['groups' => ['client:read']]);
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }



    #[Route('/{id}', name:'client_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Client $client): JsonResponse
    {
        try {
            $this->clientService->delete($client);
            return $this->json(['message' => 'Client deleted'], 204);
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/{id}', name:'client_deactivate', methods: ['PATCH'])]
    #[IsGranted('ROLE_ADMIN')]
    public function setActivation(Client $client): JsonResponse
    {
        try {
            $this->clientService->setActivation($client, !$client->isActive());
            return $this->json(['message' => 'Client updated'], 200);
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }
}
