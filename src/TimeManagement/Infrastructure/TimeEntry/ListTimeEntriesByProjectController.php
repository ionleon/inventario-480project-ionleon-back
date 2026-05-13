<?php

namespace App\TimeManagement\Infrastructure\TimeEntry;

use App\TimeManagement\Application\ListTimeEntriesByProject\ListTimeEntriesByProjectHandler;
use App\TimeManagement\Application\ListTimeEntriesByProject\ListTimeEntriesByProjectQuery;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[OA\Tag(name: 'Time Entries')]
#[Route('/projects/{id}/time-entries', name: 'project_time_entries_index', methods: ['GET'])]
#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_EMPLOYEE')"))]
final class ListTimeEntriesByProjectController extends AbstractController
{
    public function __construct(
        private readonly ListTimeEntriesByProjectHandler $handler,
    ) {}

    public function __invoke(string $id, Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        // Si no es admin, solo ve sus propias entradas
        $userId = $this->isGranted('ROLE_ADMIN') ? null : $this->getUser()?->getId()?->toRfc4122();

        try {
            $entries = $this->handler->handle(new ListTimeEntriesByProjectQuery($id, $userId, $page, $limit));

            return $this->json($entries, 200, [], ['groups' => ['time:read']]);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }
}
