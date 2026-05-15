<?php

namespace App\ProjectManagement\Infrastructure\Development\Link;

use App\ProjectManagement\Application\UpdateLink\UpdateLinkCommand;
use App\ProjectManagement\Application\UpdateLink\UpdateLinkHandler;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Links')]
// #[Route('/links/{id}', name: 'link_update', methods: ['PUT'])] — superseded by DDD Link slice controllers
final class UpdateLinkController extends AbstractController
{
    public function __construct(
        private readonly UpdateLinkHandler $handler,
    ) {}

    public function __invoke(string $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $command = new UpdateLinkCommand(
                linkId:         $id,
                url:            $data['url']           ?? null,
                enviroment:     $data['enviroment']    ?? null,
                developmentId:  $data['development_id'] ?? null,
            );

            $this->handler->handle($command);

            return $this->json([], 200);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }
}
