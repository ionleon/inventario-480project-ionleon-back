<?php

namespace App\Controller;

use App\Service\TimeEntryService;
use App\TimeManagement\Domain\TimeEntry;
use App\TimeManagement\Infrastructure\TimeEntryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/time-entries', name: 'app_time_entry')]
#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_EMPLOYEE')"))]

final class TimeEntryController extends AbstractController
{
    public function __construct(
        private TimeEntryService    $manager,
        private TimeEntryRepository $repository
    ) {}

    #[Route('', name: 'time_entry_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $timeEntries = $this->repository->findAll();
        return $this->json($timeEntries, 200, [], ['groups' => ['time:read']]);
    }

    #[Route('/{id}', name: 'time_entry_show', methods: ['GET'])]
    public function show(TimeEntry $timeEntry): JsonResponse
    {
        return $this->json($timeEntry, 200, [], ['groups' => ['time:read']]);
    }

    #[Route('', name: 'time_entry_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        try {
            $timeEntry = $this->manager->create($data);
            return $this->json([], 201, [], ['groups' => ['time:read']]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/{id}', name: 'time_entry_update', methods: ['PUT'])]
    public function update(TimeEntry $timeEntry, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        try {
            $this->manager->save($timeEntry, $data);
            return $this->json([], 200, [], ['groups' => ['time:read']]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/{id}', name: 'time_entry_delete', methods: ['DELETE'])]
    public function delete(TimeEntry $timeEntry): JsonResponse
    {
        $this->manager->delete($timeEntry);
        return $this->json(null, 204);
    }
}
