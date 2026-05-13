<?php

namespace App\TimeManagement\Infrastructure\TimeEntry;

use App\TimeManagement\Application\UpdateTimeEntry\UpdateTimeEntryCommand;
use App\TimeManagement\Application\UpdateTimeEntry\UpdateTimeEntryHandler;
use App\TimeManagement\Application\GetTimeEntry\GetTimeEntryHandler;
use App\TimeManagement\Application\GetTimeEntry\GetTimeEntryQuery;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Time Entries')]
#[Route('/time-entries/{id}', name: 'time_entries_update', methods: ['PUT'])]
final class UpdateTimeEntryController extends AbstractController
{
    public function __construct(
        private readonly UpdateTimeEntryHandler $handler,
        private readonly GetTimeEntryHandler $getHandler,
    ) {}

    public function __invoke(string $id, Request $request): JsonResponse
    {
        // Verificar que el time entry pertenece al usuario o es admin
        try {
            $timeEntry = $this->getHandler->handle(new GetTimeEntryQuery($id));

            if (!$this->isGranted('ROLE_ADMIN') && $timeEntry->getProjectUser()->getAppUser() !== $this->getUser()) {
                throw $this->createAccessDeniedException('You can only edit your own time entries.');
            }

            $data = json_decode($request->getContent(), true);

            $command = new UpdateTimeEntryCommand(
                timeEntryId: $id,
                date: $data['date'] ?? null,
                hour: $data['hour'] ?? null,
                comment: $data['comment'] ?? null,
            );

            $this->handler->handle($command);

            return $this->json([], 200);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }
}
