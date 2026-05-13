<?php

namespace App\ProjectManagement\Infrastructure\Development\Technology;

use App\ProjectManagement\Application\ListTechnologies\ListTechnologiesHandler;
use App\ProjectManagement\Application\ListTechnologies\ListTechnologiesQuery;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Technologies')]
#[Route('/technologies', name: 'technologies_index', methods: ['GET'])]
final class ListTechnologiesController extends AbstractController
{
    public function __construct(
        private readonly ListTechnologiesHandler $handler,
    ) {}

    public function __invoke(): JsonResponse
    {
        $technologies = $this->handler->handle(new ListTechnologiesQuery());

        return $this->json($technologies, 200, [], ['groups' => ['tech:read']]);
    }
}
