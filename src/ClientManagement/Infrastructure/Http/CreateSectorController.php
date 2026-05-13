<?php

namespace App\ClientManagement\Infrastructure\Http;

use App\ClientManagement\Application\CreateSector\CreateSectorCommand;
use App\ClientManagement\Application\CreateSector\CreateSectorHandler;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[OA\Tag(name: 'Client Management')]
#[Route('/sectors', name: 'app_sector_create', methods: ['POST'])]
#[IsGranted('ROLE_ADMIN')]
final class CreateSectorController extends AbstractController
{
    public function __construct(
        private readonly CreateSectorHandler $handler,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $command = new CreateSectorCommand(
                id:   $data['id']   ?? throw new \InvalidArgumentException('Id is required.'),
                name: $data['name'] ?? throw new \InvalidArgumentException('Name is required.'),
            );

            $this->handler->handle($command);

            return $this->json(['message' => 'Sector created'], 201, [], ['groups' => ['sector:read']]);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }
}
