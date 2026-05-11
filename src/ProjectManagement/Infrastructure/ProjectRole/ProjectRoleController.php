<?php

namespace App\ProjectManagement\Infrastructure\ProjectRole;

use App\ProjectManagement\Application\ProjectRole\ProjectRoleService;
use App\ProjectManagement\Domain\ProjectRole\ProjectRole;
use App\ProjectManagement\Domain\ProjectRole\ProjectRoleRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/project-roles', name: 'project_role')]
final class ProjectRoleController extends AbstractController
{

    public function __construct(
        private ProjectRoleService             $manager,
        private ProjectRoleRepositoryInterface $repository
    )
    {}


    #[Route('', name: 'project_role_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $roles = $this->repository->findAll();
        return $this->json($roles, 200, [], ['groups' => ['project_role:read']]);
    }

    #[Route('/{id}', name: 'project_role_show', methods: ['GET'])]
    public function show(ProjectRole $projectRole): JsonResponse
    {
        return $this->json($projectRole, 200, [], ['groups' => 'project_role:read']);
    }

    #[Route('', name: 'project_role_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        try {
            $role = $this->manager->create($data);
            return $this->json([], 201,  [], []);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/{id}', name: 'project_role_update', methods: ['PUT'])]
    public function update(ProjectRole $projectRole, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        try {
            $this->manager->update($projectRole, $data);
            return $this->json([], 200,  [], []);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/{id}', name: 'project_role_delete', methods: ['DELETE'])]
    public function delete(ProjectRole $projectRole): JsonResponse
    {
        try {
            $this->manager->delete($projectRole);
            return $this->json(null, 204);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }
}
