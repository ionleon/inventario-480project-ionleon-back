<?php

namespace App\Controller;

use App\Entity\AppUser;
use App\Entity\TimeEntry;
use App\Repository\TimeEntryRepository;

use App\Service\TimeEntryManager;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/users/{id}/time-entries', name: 'app_user_time_entry')]
final class UserTimeEntryController extends AbstractController
{

    public function __construct(
      private TimeEntryManager $teManager,
      private TimeEntryRepository $teRepostory
    ) {}

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(AppUser $user): Response
    {
        if(!$this->isGranted('ROLE_ADMIN') && $user !== $this->getUser()){
            throw $this->createAccessDeniedException('Cannot see hours of other users.');
        }

        $entries = $this->teRepostory->findByUser($user);
        return $this->json($entries, 200, [], ['groups' => ['time:read']]);
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
        $data = json_decode($request->getContent(), true);

        $timeEntry = $this->teManager->create(data: $data, targetUser:  $user);
        return $this->json([], 201, [], ['groups' => ['time:read']]);
    }
}
