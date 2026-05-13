<?php

namespace App\ClientManagement\Infrastructure\Http;

use App\ClientManagement\Application\GetSector\GetSectorHandler;
use App\ClientManagement\Application\GetSector\GetSectorQuery;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[OA\Tag(name: 'Client Management')]
#[Route('/sectors/{id}', name: 'app_sector_show', methods: ['GET'])]
#[IsGranted('ROLE_ADMIN')]
final class GetSectorController extends AbstractController
{
    public function __construct(
        private readonly GetSectorHandler $handler,
    ) {}

    public function __invoke(string $id): JsonResponse
    {
        try {
            $sector = $this->handler->handle(new GetSectorQuery($id));

            return $this->json($sector, 200, [], ['groups' => ['sector:read']]);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }
}
