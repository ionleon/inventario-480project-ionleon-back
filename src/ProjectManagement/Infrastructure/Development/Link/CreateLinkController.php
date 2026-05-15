<?php

namespace App\ProjectManagement\Infrastructure\Development\Link;

use App\ProjectManagement\Application\CreateLink\CreateLinkCommand;
use App\ProjectManagement\Application\CreateLink\CreateLinkHandler;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Links')]
// #[Route('/links', name: 'link_create', methods: ['POST'])] — superseded by CreateLinkController in App\App\UI\API\Controller\Link
final class CreateLinkController extends AbstractController
{
    public function __construct(
        private readonly CreateLinkHandler $handler,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $command = new CreateLinkCommand(
                id:             $data['id']          ?? throw new \InvalidArgumentException('Id is required.'),
                url:            $data['url']         ?? throw new \InvalidArgumentException('URL is required.'),
                enviroment:     $data['enviroment']  ?? throw new \InvalidArgumentException('Environment is required.'),
                developmentId:  $data['development_id'] ?? throw new \InvalidArgumentException('Development is required.'),
            );

            $this->handler->handle($command);

            return $this->json([], 201);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }
}
