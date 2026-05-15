<?php

namespace App\ClientManagement\Infrastructure\Http;

use App\ClientManagement\Application\DeleteClient\DeleteClientCommand;
use App\ClientManagement\Application\DeleteClient\DeleteClientHandler;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[OA\Tag(name: 'Client Management')]
// MIGRATED: route disabled, see src/App/UI/API/Controller/Client/
// #[Route('/clients/{id}', name: 'app_client_delete', methods: ['DELETE'])]
#[IsGranted('ROLE_ADMIN')]
final class DeleteClientController extends AbstractController
{
    public function __construct(
        private readonly DeleteClientHandler $handler,
    ) {}

    public function __invoke(string $id): JsonResponse
    {
        try {
            $this->handler->handle(new DeleteClientCommand($id));

            return $this->json(null, 204);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        } catch (\LogicException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }
}
