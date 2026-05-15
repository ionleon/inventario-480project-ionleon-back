<?php

namespace App\ProjectManagement\Infrastructure\Development\Technology;

use App\ProjectManagement\Application\UpdateTechnology\UpdateTechnologyCommand;
use App\ProjectManagement\Application\UpdateTechnology\UpdateTechnologyHandler;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

// MIGRATED: route disabled, see App\App\UI\API\Controller\Technology\
#[OA\Tag(name: 'Technologies')]
// #[Route('/technologies/{id}', name: 'technologies_update', methods: ['PATCH'])]
final class UpdateTechnologyController extends AbstractController
{
    public function __construct(
        private readonly UpdateTechnologyHandler $handler,
    ) {}

    public function __invoke(string $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $command = new UpdateTechnologyCommand(
                technologyId: $id,
                name:         $data['name'] ?? null,
            );

            $this->handler->handle($command);

            return $this->json([], 200);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }
}
