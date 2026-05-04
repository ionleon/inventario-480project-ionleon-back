<?php

namespace App\Controller;

use App\Entity\Client;
use App\Repository\ClientRepository;
use App\Repository\ContactRepository;
use App\Repository\ProjectRepository;
use App\Service\ClientManager;
use App\Service\ContactManager;
use App\Service\PaginationService;
use App\Service\ProjectManager;
use Exception;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\Uuid;


#[Route('/clients', name: 'app_client')]
#[OA\Tag(name: 'Projects')]
final class ClientController extends AbstractController
{


    public function __construct(
        private readonly ClientManager $clientManager,
        private readonly ClientRepository $clientRepository,
        private readonly ProjectRepository $projectRepository,
        private readonly ContactRepository $contactRepository,
        private readonly ContactManager $contactManager,
        private readonly PaginationService $paginationService,
    )
    {}

    /**
     * @throws Exception
     */
    #[Route('', name: 'index', methods:['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function index(Request $request): JsonResponse
    {

        $term = $request->query->get('term');
        $isActive = $request->query->has('isActive')
            ? $request->query->getBoolean('isActive')
            : null;


        $qb = $this->clientRepository->qbWithSectorsByFilters($term, $isActive);

        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        $clients = $this->paginationService->paginate($qb, $page, $limit);

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

        $qb = $this->projectRepository->qbByClient($client);
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        $projects = $this->paginationService->paginate($qb, $page, $limit);

        return $this->json($projects, 200, [], ['groups' => ['project:read']]);
    }

    /**
     * @throws Exception
     */
    #[Route('/{id}/contacts', name:'client_show_contacts', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function showContacts(Client $client, Request $request): JsonResponse
    {
        $qb = $this->contactRepository->findByClient($client);
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        $contacts = $this->paginationService->paginate($qb, $page, $limit);

        return $this->json($contacts, 200, [], ['groups' => ['contact:read']]);
    }

    #[Route('/{id}/contacts', name:'create_contacts', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function createContacts(Client $client, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $this->contactManager->create($data, $client);

        return $this->json([], 201, [], ['groups' => ['contact:read']]);
    }

    #[Route('', name: 'create_client', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $client = $this->clientManager->create($data);
            return $this->json([], 201);
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/{id}', name:'client_update', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function update(Client $client, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $this->clientManager->update($client, $data);

        return $this->json([], 201, [], ['groups' => ['client:read']]);
    }



    #[Route('/{id}', name:'client_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Client $client): JsonResponse
    {
        $this->clientManager->delete($client);
        return $this->json(null, 204);
    }

    #[Route('/{id}', name:'client_deactivate', methods: ['PATCH'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deactivate(Client $client): JsonResponse
    {
        $this->clientManager->deactivate($client);
        return $this->json(null, 204);
    }




}
