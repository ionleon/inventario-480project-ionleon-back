<?php

namespace App\ProjectManagement\Infrastructure\Development\Link;

use App\ProjectManagement\Application\DeleteLink\DeleteLinkCommand;
use App\ProjectManagement\Application\DeleteLink\DeleteLinkHandler;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Links')]
// #[Route('/links/{id}', name: 'link_delete', methods: ['DELETE'])] — superseded by DeleteLinkController in App\App\UI\API\Controller\Link
final class DeleteLinkController extends AbstractController
{
    public function __construct(
        private readonly DeleteLinkHandler $handler,
    ) {}

    public function __invoke(string $id): JsonResponse
    {
        try {
            $this->handler->handle(new DeleteLinkCommand($id));

            return $this->json(null, 204);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }
}
