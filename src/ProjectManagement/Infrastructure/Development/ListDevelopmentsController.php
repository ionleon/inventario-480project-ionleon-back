<?php

namespace App\ProjectManagement\Infrastructure\Development;

use App\ProjectManagement\Application\ListDevelopments\ListDevelopmentsHandler;
use App\ProjectManagement\Application\ListDevelopments\ListDevelopmentsQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/projects/{id}/developments', name: 'project_development_index', methods: ['GET'])]
final class ListDevelopmentsController extends AbstractController
{
    public function __construct(
        private readonly ListDevelopmentsHandler $handler,
    ) {}

    public function __invoke(string $id): JsonResponse
    {
        try {
            $developments = $this->handler->handle(new ListDevelopmentsQuery($id));

            return $this->json($developments, 200, [], ['groups' => ['dev:read']]);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }
}
