<?php

namespace App\ProjectManagement\Infrastructure\Development;

use App\ProjectManagement\Application\DeleteDevelopment\DeleteDevelopmentCommand;
use App\ProjectManagement\Application\DeleteDevelopment\DeleteDevelopmentHandler;
use App\ProjectManagement\Domain\Development\DevelopmentRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/projects/{id}/developments/{developmentId}', name: 'project_development_delete', methods: ['DELETE'])]
final class DeleteDevelopmentController extends AbstractController
{
    public function __construct(
        private readonly DeleteDevelopmentHandler $handler,
        private readonly DevelopmentRepositoryInterface $developmentRepository,
    ) {}

    public function __invoke(string $developmentId): JsonResponse
    {
        try {
            $development = $this->developmentRepository->findById($developmentId);

            if (!$development) {
                throw new \DomainException('Development not found');
            }

            $this->denyAccessUnlessGranted('PROJECT_EDIT', $development->getProject());
            $this->handler->handle(new DeleteDevelopmentCommand($developmentId));

            return $this->json(null, 204);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }
}
