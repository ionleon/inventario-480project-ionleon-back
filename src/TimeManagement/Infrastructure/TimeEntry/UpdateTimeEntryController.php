<?php

namespace App\TimeManagement\Infrastructure\TimeEntry;

use App\Shared\Infrastructure\Http\AppController;
use App\TimeManagement\Application\UpdateTimeEntry\UpdateTimeEntryCommand;
use App\TimeManagement\Application\UpdateTimeEntry\UpdateTimeEntryHandler;
use App\TimeManagement\Application\GetTimeEntry\GetTimeEntryHandler;
use App\TimeManagement\Application\GetTimeEntry\GetTimeEntryQuery;
use App\TimeManagement\Infrastructure\TimeEntry\Request\UpdateTimeEntryRequest;
use App\TimeManagement\Infrastructure\TimeEntry\Response\TimeEntryResponse;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[OA\Tag(name: 'Time Entries')]
#[Route('/time-entries/{id}', name: 'time_entries_update', methods: ['PUT'])]
final class UpdateTimeEntryController extends AppController
{
    public function __construct(
        private readonly UpdateTimeEntryHandler $handler,
        private readonly GetTimeEntryHandler $getHandler,
        private readonly ValidatorInterface $validator,
    ) {}

    public function __invoke(string $id, Request $request): JsonResponse
    {
        // Verificar que el time entry pertenece al usuario o es admin
        try {
            $timeEntry = $this->getHandler->handle(new GetTimeEntryQuery($id));

            if (!$this->isGranted('ROLE_ADMIN') && $timeEntry->getProjectUser()->getAppUser() !== $this->getUser()) {
                throw $this->createAccessDeniedException('You can only edit your own time entries.');
            }

            $dto = UpdateTimeEntryRequest::fromRequest($request);

            $violations = $this->validator->validate($dto);
            if (count($violations) > 0) {
                return $this->json(['errors' => $this->formatViolations($violations)], 400);
            }

            $command = new UpdateTimeEntryCommand(
                timeEntryId: $id,
                date: $dto->date,
                hour: $dto->hour,
                comment: $dto->comment,
            );

            $updatedTimeEntry = $this->handler->handle($command);

            return $this->json(TimeEntryResponse::fromEntity($updatedTimeEntry), 200);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }
}
