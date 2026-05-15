<?php

namespace App\ProjectManagement\Infrastructure\Development;

use App\ProjectManagement\Application\CreateDevelopment\CreateDevelopmentCommand;
use App\ProjectManagement\Application\CreateDevelopment\CreateDevelopmentHandler;
use App\ProjectManagement\Application\GetProject\GetProjectHandler;
use App\ProjectManagement\Application\GetProject\GetProjectQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/projects/{id}/developments', name: 'project_development_create', methods: ['POST'])]
final class CreateDevelopmentController extends AbstractController
{
    public function __construct(
        private readonly CreateDevelopmentHandler $handler,
        private readonly GetProjectHandler $getProjectHandler,
    ) {}

    public function __invoke(string $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $project = $this->getProjectHandler->handle(new GetProjectQuery($id));
            $this->denyAccessUnlessGranted('PROJECT_EDIT', $project);

            $command = new CreateDevelopmentCommand(
                projectId:      $id,
                id:             $data['id']             ?? throw new \InvalidArgumentException('Id is required.'),
                technologyId:   $data['technology_id']  ?? throw new \InvalidArgumentException('Technology is required.'),
                name:           $data['name']           ?? throw new \InvalidArgumentException('Name is required.'),
                description:    $data['description']    ?? throw new \InvalidArgumentException('Description is required.'),
                urlRepository:  $data['url_repository'] ?? throw new \InvalidArgumentException('Repository URL is required.'),
                links:          $data['links']          ?? null,
            );

            $this->handler->handle($command);

            return $this->json([], 201, [], ['groups' => ['dev:read']]);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }
}
