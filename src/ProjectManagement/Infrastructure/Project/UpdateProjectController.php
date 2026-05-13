<?php

namespace App\ProjectManagement\Infrastructure\Project;

use App\ProjectManagement\Application\UpdateProject\UpdateProjectCommand;
use App\ProjectManagement\Application\UpdateProject\UpdateProjectHandler;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Projects')]
#[Route('/projects/{id}', name: 'project_edit', methods: ['PUT'])]
final class UpdateProjectController extends AbstractController
{
    public function __construct(
        private readonly UpdateProjectHandler $handler,
    ) {}

    public function __invoke(string $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $command = new UpdateProjectCommand(
                projectId:   $id,
                name:        $data['name']        ?? throw new \InvalidArgumentException('Name is required.'),
                description: $data['description'] ?? throw new \InvalidArgumentException('Description is required.'),
                clientId:    $data['client_id']   ?? throw new \InvalidArgumentException('Client is required.'),
                startDate:   $data['start_date']  ?? null,
                isActive:    $data['is_active']   ?? true,
            );

            $this->handler->handle($command);

            return $this->json([], 200, [], ['groups' => ['project:read']]);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }
}
