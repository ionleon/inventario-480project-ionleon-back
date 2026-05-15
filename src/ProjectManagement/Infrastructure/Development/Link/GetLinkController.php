<?php

namespace App\ProjectManagement\Infrastructure\Development\Link;

use App\ProjectManagement\Application\GetLink\GetLinkHandler;
use App\ProjectManagement\Application\GetLink\GetLinkQuery;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Links')]
#[Route('/links/{id}', name: 'link_show', methods: ['GET'])]
final class GetLinkController extends AbstractController
{
    public function __construct(
        private readonly GetLinkHandler $handler,
    ) {}

    public function __invoke(string $id): JsonResponse
    {
        try {
            $link = $this->handler->handle(new GetLinkQuery($id));

            return $this->json($link, 200, [], ['groups' => ['link:read']]);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }
}
