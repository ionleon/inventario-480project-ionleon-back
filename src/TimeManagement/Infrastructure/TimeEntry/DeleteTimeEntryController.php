<?php

namespace App\TimeManagement\Infrastructure\TimeEntry;

use App\Shared\Infrastructure\Http\AppController;
use App\TimeManagement\Application\DeleteTimeEntry\DeleteTimeEntryCommand;
use App\TimeManagement\Application\DeleteTimeEntry\DeleteTimeEntryHandler;
use App\TimeManagement\Application\GetTimeEntry\GetTimeEntryHandler;
use App\TimeManagement\Application\GetTimeEntry\GetTimeEntryQuery;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Time Entries')]
#[Route('/time-entries/{id}', name: 'time_entries_delete', methods: ['DELETE'])]
final class DeleteTimeEntryController extends AppController
{
    public function __construct(
        private readonly DeleteTimeEntryHandler $handler,
        private readonly GetTimeEntryHandler $getHandler,
    ) {}

    public function __invoke(string $id): JsonResponse
    {
        // Verificar que el time entry pertenece al usuario o es admin
        try {
            $timeEntry = $this->getHandler->handle(new GetTimeEntryQuery($id));

            if (!$this->isGranted('ROLE_ADMIN') && $timeEntry->getProjectUser()->getAppUser() !== $this->getUser()) {
                throw $this->createAccessDeniedException('You can only delete your own time entries.');
            }

            $this->handler->handle(new DeleteTimeEntryCommand($id));

            return $this->json(null, 204);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }
}
