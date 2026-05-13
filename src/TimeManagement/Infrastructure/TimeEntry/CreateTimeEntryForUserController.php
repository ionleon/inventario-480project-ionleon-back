<?php

namespace App\TimeManagement\Infrastructure\TimeEntry;

use App\TimeManagement\Application\CreateTimeEntry\CreateTimeEntryCommand;
use App\TimeManagement\Application\CreateTimeEntry\CreateTimeEntryHandler;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Time Entries')]
#[Route('/users/{id}/time-entries', name: 'user_time_entries_create', methods: ['POST'])]
final class CreateTimeEntryForUserController extends AbstractController
{
    public function __construct(
        private readonly CreateTimeEntryHandler $handler,
    ) {}

    public function __invoke(string $id, Request $request): JsonResponse
    {
        // Verificar que el usuario puede crear sus propias entradas o es admin
        if (!$this->isGranted('ROLE_ADMIN') && $id !== $this->getUser()?->getId()?->toRfc4122()) {
            throw $this->createAccessDeniedException('Cannot create hours for other users.');
        }

        $data = json_decode($request->getContent(), true);

        try {
            $command = new CreateTimeEntryCommand(
                id: $data['id'] ?? throw new \InvalidArgumentException('id is required.'),
                date: $data['date'] ?? throw new \InvalidArgumentException('date is required.'),
                hour: $data['hour'] ?? throw new \InvalidArgumentException('hour is required.'),
                comment: $data['comment'] ?? null,
                projectUserId: $data['project_user_id'] ?? null,
                projectId: $data['project_id'] ?? null,
                userId: $id,
            );

            $this->handler->handle($command, $id);

            return $this->json(['message' => 'Time entry created'], 201);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        } catch (\DomainException | \LogicException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }
}
