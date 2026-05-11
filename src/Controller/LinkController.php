<?php

namespace App\Controller;

use App\ProjectManagement\Application\Developments\Link\LinkService;
use App\ProjectManagement\Domain\Developments\Link\Link;
use App\Repository\LinkRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/links', name: 'app_link')]
final class LinkController extends AbstractController
{
    public function __construct(
        private LinkService    $manager,
        private LinkRepository $repository
    ) {}

    #[Route('', name: 'link_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $links = $this->repository->findAll();
        return $this->json($links, 200, [], ['groups' => 'link:read']);
    }

    #[Route('/{id}', name: 'link_show', methods: ['GET'])]
    public function show(Link $link): JsonResponse
    {
        return $this->json($link, 200, [], ['groups' => 'link:read']);
    }

    #[Route('',name: 'link_create', methods:['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data =  json_decode($request->getContent(), true);
        try {
            $link = $this->manager->create($data);
            return $this->json([], 201, [], []);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/{id}',name: 'link_update', methods:['PUT'])]
    public function update(Link $link,Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        try {
            $this->manager->save($link, $data);
            return $this->json([], 201, [], []);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/{id}',name: 'link_delete', methods:['DELETE'])]
    public function delete(Link $link): JsonResponse
    {
        $this->manager->delete($link);
        return $this->json(null, 204);
    }


}
