<?php

namespace App\Controller;

use App\Repository\TimeEntryRepository;
use App\Service\TimeEntryManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/projects/{id}/time-entries', name: 'app_project_time_entry')]
final class ProjectTimeEntryController extends AbstractController
{
    public function __construct(
        private TimeEntryManager $teManager,
        private TimeEntryRepository $teRepository
    )
    {}

    #[Route('', name: 'project_time_entry_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('project_time_entry/index.html.twig', [
            'controller_name' => 'ProjectTimeEntryController',
        ]);
    }
}
