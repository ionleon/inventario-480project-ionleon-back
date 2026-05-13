<?php

namespace App\ProjectManagement\Infrastructure\Development;

use App\ProjectManagement\Application\GetProject\GetProjectHandler;
use App\ProjectManagement\Application\GetProject\GetProjectQuery;
use App\ProjectManagement\Application\UpdateDevelopment\UpdateDevelopmentCommand;
use App\ProjectManagement\Application\UpdateDevelopment\UpdateDevelopmentHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/projects/{id}/developments/{developmentId}', name: 'project_development_update', methods: ['PUT'])]
final class UpdateDevelopmentController extends AbstractController
{
    public function __construct(
        private readonly UpdateDevelopmentHandler $handler,
        private readonly GetProjectHandler $getProjectHandler,
    ) {}

    public function __invoke(string $id, string $developmentId, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $project = $this->getProjectHandler->handle(new GetProjectQuery($id));
            $this->denyAccessUnlessGranted('PROJECT_EDIT', $project);

            $command = new UpdateDevelopmentCommand(
                developmentId:  $developmentId,
                technologyId:   $data['technology_id']  ?? null,
                name:           $data['name']           ?? null,
                description:    $data['description']    ?? null,
                urlRepository:  $data['url_repository'] ?? null,
                links:          $data['links']          ?? null,
            );

            $this->handler->handle($command);

            return $this->json([], 200, [], ['groups' => ['dev:read']]);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }
}
