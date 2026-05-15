<?php

namespace App\ProjectManagement\Infrastructure\ProjectRole;

use App\ProjectManagement\Application\CreateProjectRole\CreateProjectRoleCommand;
use App\ProjectManagement\Application\CreateProjectRole\CreateProjectRoleHandler;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Project Roles')]
// MIGRATED to App\App\UI\API\Controller\ProjectRole\CreateProjectRole\CreateProjectRoleController
// #[Route('/project-roles', name: 'project_role_create', methods: ['POST'])]
final class CreateProjectRoleController extends AbstractController
{
    public function __construct(
        private readonly CreateProjectRoleHandler $handler,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $command = new CreateProjectRoleCommand(
                id:   $data['id']   ?? throw new \InvalidArgumentException('Id is required.'),
                name: $data['name'] ?? throw new \InvalidArgumentException('Name is required.'),
            );

            $this->handler->handle($command);

            return $this->json([], 201);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }
}
