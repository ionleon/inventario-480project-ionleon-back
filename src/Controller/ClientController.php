<?php

namespace App\Controller;

use App\Entity\Client;
use App\Repository\ClientRepository;
use App\Service\ClientManager;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/clients', name: 'app_client')]
#[OA\Tag(name: 'Projects')]
final class ClientController extends AbstractController
{


    public function __construct(private readonly ClientManager $clientManager)
    {}

    #[Route('', name: 'client_index', methods:['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function index(Request $request, ClientRepository $clientRepository): JsonResponse
    {
        $term = $request->query->get('term');
        $isActive = $request->query->has('isActive')
                    ? $request->query->getBoolean('isActive')
                    : null;

        $clients = $clientRepository->findWithSectorsByFilters($term, $isActive);

        return $this->json($clients, 200, [], ['groups' => ['client:read']]);
    }

    #[Route('', name: 'create_client', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $client = $this->clientManager->create($data);
            return $this->json([], 201);
        } catch (\Exception $e) {
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
