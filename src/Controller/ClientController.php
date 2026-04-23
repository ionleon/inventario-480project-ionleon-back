<?php

namespace App\Controller;

use App\Repository\ClientRepository;
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

}
