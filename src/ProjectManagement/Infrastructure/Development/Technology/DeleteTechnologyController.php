<?php

namespace App\ProjectManagement\Infrastructure\Development\Technology;

use App\ProjectManagement\Application\DeleteTechnology\DeleteTechnologyCommand;
use App\ProjectManagement\Application\DeleteTechnology\DeleteTechnologyHandler;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

// MIGRATED: route disabled, see App\App\UI\API\Controller\Technology\DeleteTechnology\DeleteTechnologyController
#[OA\Tag(name: 'Technologies')]
// #[Route('/technologies/{id}', name: 'technologies_delete', methods: ['DELETE'])]
final class DeleteTechnologyController extends AbstractController
{
    public function __construct(
        private readonly DeleteTechnologyHandler $handler,
    ) {}

    public function __invoke(string $id): JsonResponse
    {
        try {
            $this->handler->handle(new DeleteTechnologyCommand($id));

            return $this->json(null, 204);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        } catch (\LogicException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }
}
