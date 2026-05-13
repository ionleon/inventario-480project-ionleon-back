<?php

namespace App\TimeManagement\Infrastructure\TimeEntry;

use App\TimeManagement\Application\ListTimeEntriesByUser\ListTimeEntriesByUserHandler;
use App\TimeManagement\Application\ListTimeEntriesByUser\ListTimeEntriesByUserQuery;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Time Entries')]
#[Route('/users/{id}/time-entries', name: 'user_time_entries_index', methods: ['GET'])]
final class ListTimeEntriesByUserController extends AbstractController
{
    public function __construct(
        private readonly ListTimeEntriesByUserHandler $handler,
    ) {}

    public function __invoke(string $id, Request $request): JsonResponse
    {
        // Verificar que el usuario puede ver sus propias entradas o es admin
        if (!$this->isGranted('ROLE_ADMIN') && $id !== $this->getUser()?->getId()?->toRfc4122()) {
            throw $this->createAccessDeniedException('Cannot see hours of other users.');
        }

        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        try {
            $result = $this->handler->handle(new ListTimeEntriesByUserQuery($id, $page, $limit));

            return $this->json($result, 200, [], ['groups' => ['dash:read']]);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }
}
