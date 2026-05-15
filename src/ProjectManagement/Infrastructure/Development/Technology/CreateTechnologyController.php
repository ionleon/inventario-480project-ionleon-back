<?php

namespace App\ProjectManagement\Infrastructure\Development\Technology;

use App\ProjectManagement\Application\CreateTechnology\CreateTechnologyCommand;
use App\ProjectManagement\Application\CreateTechnology\CreateTechnologyHandler;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

// MIGRATED: route disabled, see App\App\UI\API\Controller\Technology\CreateTechnology\CreateTechnologyController
#[OA\Tag(name: 'Technologies')]
// #[Route('/technologies', name: 'technologies_create', methods: ['POST'])]
final class CreateTechnologyController extends AbstractController
{
    public function __construct(
        private readonly CreateTechnologyHandler $handler,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $command = new CreateTechnologyCommand(
                id:   $data['id']   ?? throw new \InvalidArgumentException('Id is required.'),
                name: $data['name'] ?? throw new \InvalidArgumentException('Name is required.'),
            );

            $this->handler->handle($command);

            return $this->json([], 200);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }
}
