<?php

namespace App\ClientManagement\Infrastructure\Http;

use App\ClientManagement\Application\ListSectors\ListSectorsHandler;
use App\ClientManagement\Application\ListSectors\ListSectorsQuery;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[OA\Tag(name: 'Client Management')]
// MIGRATED: route disabled, see src/App/UI/API/Controller/Sector/
// #[Route('/sectors', name: 'app_sector_index', methods: ['GET'])]
#[IsGranted('ROLE_ADMIN')]
final class ListSectorsController extends AbstractController
{
    public function __construct(
        private readonly ListSectorsHandler $handler,
    ) {}

    public function __invoke(): JsonResponse
    {
        $sectors = $this->handler->handle(new ListSectorsQuery());

        return $this->json($sectors, 200, [], ['groups' => ['sector:read']]);
    }
}
