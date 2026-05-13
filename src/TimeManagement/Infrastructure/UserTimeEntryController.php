<?php

namespace App\TimeManagement\Infrastructure;

use App\Service\PaginationService;
use App\TimeManagement\Application\TimeEntryService;
use App\TimeManagement\Domain\TimeEntryRepositoryInterface;
use App\UserManagement\Domain\AppUser;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/users/{id}/time-entries', name: 'app_user_time_entry')]
final class UserTimeEntryController extends AbstractController
{

    public function __construct(
      private readonly TimeEntryService             $timeEntryService,
      private readonly TimeEntryRepositoryInterface $timeEntryRepository
    ) {}

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(AppUser $user, Request $request): Response
    {
        if(!$this->isGranted('ROLE_ADMIN') && $user !== $this->getUser()){
            throw $this->createAccessDeniedException('Cannot see hours of other users.');
        }

        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        $entries = $this->timeEntryRepository->findByUserPaginated($user, $page, $limit);

        #Provisional method, might change in the future

        $totalHours = $this->timeEntryRepository->getTotalHoursByUser($user);

        return $this->json([
            'total_hours' => $totalHours,
            'data' => $entries
        ], 200, [], ['groups' => ['dash:read']]);
    }

    /**
     * @throws Exception
     */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(AppUser $user, Request $request): Response
    {
        if(!$this->isGranted('ROLE_ADMIN') && $user !== $this->getUser()){
            throw $this->createAccessDeniedException('Cannot see hours of other users.');
        }
        {
            $data = json_decode($request->getContent(), true);

            $this->timeEntryService->create(data: $data, user: $user);
            return $this->json(['message' => 'Time entry created'], 201, [], ['groups' => ['time:read']]);
        }
    }
}
