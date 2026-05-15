<?php

namespace App\TimeManagement\Infrastructure\TimeEntry;

use App\Shared\Infrastructure\Http\AppController;
use App\TimeManagement\Application\CreateTimeEntry\CreateTimeEntryCommand;
use App\TimeManagement\Application\CreateTimeEntry\CreateTimeEntryHandler;
use App\TimeManagement\Infrastructure\TimeEntry\Request\CreateTimeEntryRequest;
use App\TimeManagement\Infrastructure\TimeEntry\Response\TimeEntryResponse;
use OpenApi\Attributes as OA;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[OA\Tag(name: 'Time Entries')]
#[Route('/projects/{id}/time-entries', name: 'project_time_entries_create', methods: ['POST'])]
#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_EMPLOYEE')"))]
final class CreateTimeEntryForProjectController extends AppController
{
    public function __construct(
        private readonly CreateTimeEntryHandler $handler,
        private readonly ValidatorInterface $validator,
    ) {}

    public function __invoke(string $id, Request $request): JsonResponse
    {
        $currentUserId = $this->getUser()?->getId()?->toRfc4122();

        $dto = CreateTimeEntryRequest::fromRequest($request);

        $violations = $this->validator->validate($dto);
        if (count($violations) > 0) {
            return $this->json(['errors' => $this->formatViolations($violations)], 400);
        }

        try {
            $command = new CreateTimeEntryCommand(
                id: $dto->id,
                date: $dto->date,
                hour: $dto->hour,
                comment: $dto->comment,
                projectUserId: $dto->projectUserId,
                projectId: $id,
                userId: $currentUserId,
            );

            $timeEntry = $this->handler->handle($command, $currentUserId);

            return $this->json(TimeEntryResponse::fromEntity($timeEntry), 201);
        } catch (\DomainException | \LogicException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }
}
