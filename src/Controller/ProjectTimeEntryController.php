<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\TimeEntry;
use App\Repository\TimeEntryRepository;
use App\Service\TimeEntryManager;
use Exception;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/projects/{id}/time-entries', name: 'app_project_time_entry')]
#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_EMPLOYEE')"))]
final class ProjectTimeEntryController extends AbstractController
{
    public function __construct(
        private TimeEntryManager $teManager,
        private TimeEntryRepository $teRepository
    ) {}

    #[Route('', name: 'project_time_entry_index', methods: ['GET'])]
    public function index(Project $project): JsonResponse
    {
        $user = $this->isGranted('ROLE_ADMIN') ? null : $this->getUser();

        $timeEntries = $this->teManager->getAllByProjects($project, $user);

        return $this->json($timeEntries, 200, [], ['groups' => 'time:read']);
    }

    /**
     * @throws Exception
     */
    #[Route('', name: 'project_time_entry_create', methods: ['POST'])]
    public function create(Project $project, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $timeEntry = $this->teManager->create($data);
        return $this->json([], 201, [], ['groups' => 'time:read']);
    }

    /**
     * @throws Exception
     */
    #[Route('{timeEntryId}', name: 'project_time_entry_update', methods: ['PUT'])]
    public function update(
        Project $project,
        #[MapEntity(mapping: ['id' => 'timeEntryId'])] TimeEntry $timeEntry,
        Request $request
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $timeEntry = $this->teManager->save($timeEntry, $data);
        return $this->json([], 201, [], ['groups' => 'time:read']);
    }
}
