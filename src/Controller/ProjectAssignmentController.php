<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\ProjectUser;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/projects/{id}/users')]
#[OA\OA\Tag(name: 'Project Assigments')]
final class ProjectAssignmentController extends AbstractController
{
    #[Route('', name: 'project_users_index', methods: ['GET'])]
    public function index(Project $project): JsonResponse
    {
        return $this->json($project->getProjectUsers(), 200, [], ['groups' => 'project:read']);
    }

    #[Route('', name: 'project_users_update', methods: ['PUT'])]
    public function update(
        Project $project,
        Request $request,
        ProjectAssigmentManager $assigmentManager
    ): JsonRespose{}
}
