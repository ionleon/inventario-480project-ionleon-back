<?php

namespace App\ProjectManagement\Infrastructure\Development\Link;

use App\ProjectManagement\Application\ListLinks\ListLinksHandler;
use App\ProjectManagement\Application\ListLinks\ListLinksQuery;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Links')]
// #[Route('/links', name: 'link_index', methods: ['GET'])] — superseded by ListLinksByProjectController in App\App\UI\API\Controller\Link
final class ListLinksController extends AbstractController
{
    public function __construct(
        private readonly ListLinksHandler $handler,
    ) {}

    public function __invoke(): JsonResponse
    {
        $links = $this->handler->handle(new ListLinksQuery());

        return $this->json($links, 200, [], ['groups' => ['link:read']]);
    }
}
