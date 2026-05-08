<?php

namespace App\Controller;

use App\Entity\TimeEntry;
use App\ProjectManagement\Domain\Project\Project;
use App\Repository\ProjectUserRepository;
use App\Repository\TimeEntryRepository;
use App\Service\PaginationService;
use App\Service\TimeEntryManager;
use Exception;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/projects/{id}/time-entries', name: 'app_project_time_entry')]
#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_EMPLOYEE')"))]
final class ProjectTimeEntryController extends AbstractController
{
    public function __construct(
        private TimeEntryManager $teManager,
        private TimeEntryRepository $teRepository,
        private ProjectUserRepository $puRepository,
        private readonly PaginationService $paginationService

    ) {}

    /**
     * @throws Exception
     */
    #[Route('', name: 'project_time_entry_index', methods: ['GET'])]
    public function index(Project $project, Request $request): JsonResponse
    {
        $user = $this->isGranted('ROLE_ADMIN') ? null : $this->getUser();

        $qb = $this->teRepository->qbByProjectAndUser($project, $user);

        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        $timeEntries = $this->paginationService->paginate($qb, $page, $limit);

        return $this->json($timeEntries, 200, [], ['groups' => 'time:read']);
    }

    /**
     * @throws Exception
     */
    #[Route('', name: 'project_time_entry_create', methods: ['POST'])]
    public function create(Project $project, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $timeEntry = $this->teManager->create($data, $project);

        return $this->json([], 201, [], ['groups' => 'time:read']);
    }

    /**
     * @throws Exception
     */
    #[Route('/{timeEntryId}', name: 'project_time_entry_update', methods: ['PUT'])]
    public function update(
        Project $project,
        #[MapEntity(mapping: ['id' => 'timeEntryId'])] TimeEntry $timeEntry,
        Request $request
    ): JsonResponse
    {
        if ($timeEntry->getProjectUser()->getProject() !== $project) {
            throw $this->createAccessDeniedException('This time entry does not belong to this project.');
        }

        if (!$this->isGranted('ROLE_ADMIN') && $timeEntry->getProjectUser()->getAppUser() !== $this->getUser()){
            throw $this->createAccessDeniedException('You can only edit your own time entries.');
        }

        $data = json_decode($request->getContent(), true);
        $timeEntry = $this->teManager->save($timeEntry, $data);
        return $this->json([], 200, [], ['groups' => 'time:read']);
    }

    #[Route('/{timeEntryId}', name: 'project_time_entry_delete', methods: ['DELETE'])]
    public function delete(
        Project $project,
        #[MapEntity(mapping: ['id' => 'timeEntryId'])] TimeEntry $timeEntry,
    ): JsonResponse
    {
        if ($timeEntry->getProjectUser()->getProject() !== $project) {
            throw $this->createAccessDeniedException('This development does not belong to this project.');
        }

        if (!$this->isGranted('ROLE_ADMIN') && $timeEntry->getProjectUser()->getAppUser() !== $this->getUser()){
            throw $this->createAccessDeniedException('You can only delete your own time entries.');
        }

        $this->teManager->delete($timeEntry);
        return $this->json(null, 204, [], []);
    }



}
