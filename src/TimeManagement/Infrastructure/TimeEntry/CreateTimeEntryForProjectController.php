<?php

namespace App\TimeManagement\Infrastructure\TimeEntry;

use App\TimeManagement\Application\CreateTimeEntry\CreateTimeEntryCommand;
use App\TimeManagement\Application\CreateTimeEntry\CreateTimeEntryHandler;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[OA\Tag(name: 'Time Entries')]
#[Route('/projects/{id}/time-entries', name: 'project_time_entries_create', methods: ['POST'])]
#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_EMPLOYEE')"))]
final class CreateTimeEntryForProjectController extends AbstractController
{
    public function __construct(
        private readonly CreateTimeEntryHandler $handler,
    ) {}

    public function __invoke(string $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $currentUserId = $this->getUser()?->getId()?->toRfc4122();

        try {
            $command = new CreateTimeEntryCommand(
                id: $data['id'] ?? throw new \InvalidArgumentException('id is required.'),
                date: $data['date'] ?? throw new \InvalidArgumentException('date is required.'),
                hour: $data['hour'] ?? throw new \InvalidArgumentException('hour is required.'),
                comment: $data['comment'] ?? null,
                projectUserId: $data['project_user_id'] ?? null,
                projectId: $id,
                userId: $currentUserId,
            );

            $this->handler->handle($command, $currentUserId);

            return $this->json(['message' => 'Time entry created'], 201);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        } catch (\DomainException | \LogicException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }
}
