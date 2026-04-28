<?php

namespace App\Controller;

use App\Repository\TimeEntryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/time-entries', name: 'app_time_entry')]
#[IsGranted('ROLE_USER')]
final class TimeEntryController extends AbstractController
{
    public function __construct(
        private TimeEntryManager $manager,
        private TimeEntryRepository $repository
    ) {}

    #[Route('', name: 'time_entry_index', methods: ['GET'])]
    public function index(): Response
    {
        $timeEntries = $this->repository->findAll();
        return $this->json($timeEntries, 200, [], ['groups' => ['time:read']]);
    }
}
