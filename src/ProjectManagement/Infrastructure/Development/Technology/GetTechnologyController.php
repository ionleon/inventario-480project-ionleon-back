<?php

namespace App\ProjectManagement\Infrastructure\Development\Technology;

use App\ProjectManagement\Application\GetTechnology\GetTechnologyHandler;
use App\ProjectManagement\Application\GetTechnology\GetTechnologyQuery;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

// MIGRATED: route disabled, see App\App\UI\API\Controller\Technology\
#[OA\Tag(name: 'Technologies')]
// #[Route('/technologies/{id}', name: 'technology_show', methods: ['GET'])]
final class GetTechnologyController extends AbstractController
{
    public function __construct(
        private readonly GetTechnologyHandler $handler,
    ) {}

    public function __invoke(string $id): JsonResponse
    {
        try {
            $technology = $this->handler->handle(new GetTechnologyQuery($id));

            return $this->json($technology, 200, [], ['groups' => ['tech:read']]);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }
}
