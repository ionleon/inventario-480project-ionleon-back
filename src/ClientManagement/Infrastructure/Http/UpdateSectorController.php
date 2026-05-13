<?php

namespace App\ClientManagement\Infrastructure\Http;

use App\ClientManagement\Application\UpdateSector\UpdateSectorCommand;
use App\ClientManagement\Application\UpdateSector\UpdateSectorHandler;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[OA\Tag(name: 'Client Management')]
#[Route('/sectors/{id}', name: 'app_sector_update', methods: ['PATCH'])]
#[IsGranted('ROLE_ADMIN')]
final class UpdateSectorController extends AbstractController
{
    public function __construct(
        private readonly UpdateSectorHandler $handler,
    ) {}

    public function __invoke(string $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $command = new UpdateSectorCommand(
                sectorId: $id,
                name:     $data['name'] ?? null,
            );

            $this->handler->handle($command);

            return $this->json(['message' => 'Sector updated'], 200, [], ['groups' => ['sector:read']]);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }
}
