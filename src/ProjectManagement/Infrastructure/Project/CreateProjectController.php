<?php

namespace App\ProjectManagement\Infrastructure\Project;

use App\ProjectManagement\Application\CreateProject\CreateProjectCommand;
use App\ProjectManagement\Application\CreateProject\CreateProjectHandler;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Projects')]
#[Route('/projects', name: 'project_create', methods: ['POST'])]
final class CreateProjectController extends AbstractController
{
    public function __construct(
        private readonly CreateProjectHandler $handler,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $command = new CreateProjectCommand(
                id:          $data['id']          ?? throw new \InvalidArgumentException('Id is required.'),
                name:        $data['name']        ?? throw new \InvalidArgumentException('Name is required.'),
                description: $data['description'] ?? throw new \InvalidArgumentException('Description is required.'),
                clientId:    $data['client_id']   ?? throw new \InvalidArgumentException('Client is required.'),
                startDate:   $data['start_date']  ?? null,
            );

            $this->handler->handle($command);

            return $this->json([], 201, [], ['groups' => ['project:read']]);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }
}
