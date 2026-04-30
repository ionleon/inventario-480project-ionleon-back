<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserTimeEntryController extends AbstractController
{
    #[Route('/user/time/entry', name: 'app_user_time_entry')]
    public function index(): Response
    {
        return $this->render('user_time_entry/index.html.twig', [
            'controller_name' => 'UserTimeEntryController',
        ]);
    }
}
