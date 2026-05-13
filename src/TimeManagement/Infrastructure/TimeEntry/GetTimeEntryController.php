<?php

namespace App\TimeManagement\Infrastructure\TimeEntry;

use App\TimeManagement\Application\GetTimeEntry\GetTimeEntryHandler;
use App\TimeManagement\Application\GetTimeEntry\GetTimeEntryQuery;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Time Entries')]
#[Route('/time-entries/{id}', name: 'time_entries_show', methods: ['GET'])]
final class GetTimeEntryController extends AbstractController
{
    public function __construct(
        private readonly GetTimeEntryHandler $handler,
    ) {}

    public function __invoke(string $id): JsonResponse
    {
        try {
            $timeEntry = $this->handler->handle(new GetTimeEntryQuery($id));

            // Verificar acceso
            if (!$this->isGranted('ROLE_ADMIN') && $timeEntry->getProjectUser()->getAppUser() !== $this->getUser()) {
                throw $this->createAccessDeniedException('You can only view your own time entries.');
            }

            return $this->json($timeEntry, 200, [], ['groups' => ['time:read']]);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }
}
