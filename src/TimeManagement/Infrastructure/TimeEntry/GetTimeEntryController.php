<?php

namespace App\TimeManagement\Infrastructure\TimeEntry;

use App\Shared\Infrastructure\Http\AppController;
use App\TimeManagement\Application\GetTimeEntry\GetTimeEntryHandler;
use App\TimeManagement\Application\GetTimeEntry\GetTimeEntryQuery;
use App\TimeManagement\Infrastructure\TimeEntry\Response\TimeEntryResponse;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Time Entries')]
// #[Route('/time-entries/{id}', name: 'time_entries_show', methods: ['GET'])]
final class GetTimeEntryController extends AppController
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

            return $this->json(TimeEntryResponse::fromEntity($timeEntry), 200);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }
}
