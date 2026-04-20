<?php

namespace App\Controller;

use App\Entity\AppUser;
use App\Repository\AppUserRepository;
use App\Service\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints\Json;
use Symfony\Component\Validator\Validator\ValidatorInterface;


/*
 * Cambiar controllers para que utilizen servicios, adaptarlos a arquitectura hexagonal a futuro
 * */


#[Route('/users')]
final class UserController extends AbstractController
{
    #[Route('', name: 'app_user_index', methods: ['GET'])]
    public function index(AppUserRepository $repository): JsonResponse
    {
        $users = $repository->findAll();

        return $this->json($users, 200, [], ['groups' => 'user:read']);
    }
    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(Uuid  $id, AppUserRepository $repository): JsonResponse
    {
        $user = $repository -> find($id);

        if (!$user) {
            return $this->json(['error' => 'User not found'], 404);
        }

        return $this->json($user, 200, [], ['groups' => 'user:read']);
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('',name: 'app_user_create', methods: ['POST'])]
    public function create(
        Request $request,
        SerializerInterface $serializer,
        UserManager $userManager,
        ValidatorInterface $validator
    ): JsonResponse
    {
        $user = $serializer->deserialize(
            $request->getContent(),
            AppUser::class,
            'json',
            ['groups' => ['user:write']]
        );

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            return $this->json($errors, 400);
        }

        $data = json_decode($request->getContent(), true);
        $plainPassword = $data['password'] ?? '';

        $userManager->create($user, $plainPassword);

        return $this->json($user, 201, [], ['groups' => 'user:read']);
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/{id}', name: 'app_user_edit', methods: ['PUT'])]
    public function edit(
        Uuid  $id,
        AppUserRepository $repository,
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    ): JsonResponse
    {
        $user = $repository -> find($id);

        if (!$user) {
            return $this->json(['error' => 'User not found'], 404);
        }

        $serializer->deserialize(
            $request->getContent(),
            AppUser::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $user,
            'groups' => ['user:write']
            ]
        );

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            return $this->json($errors, 400);
        }

        $entityManager->flush();

        return $this->json($user, 200, [], ['groups' => 'user:read']);

    }

    public function delete(Uuid $id, AppUserRepository $repository, EntityManagerInterface $em): JsonResponse
    {
        $user = $repository->find($id);
        if (!$user) return $this->json(['error' => 'User not found'], 404);

        $em->remove($user);
        $em->flush();

        return $this->json(null, 204);
    }

    public function deactivate(
        Uuid $id,
        AppUserRepository $repository,
        UserManager $userManager
    ) : JsonResponse {

        $user = $repository -> find($id);

        if(!$user) {
            return $this->json(['error' => 'User not found'], 404);
        }

        $userManager->deactivate($user);

        return $this->json([
            'message' => 'User has been deactivated',
            'id' => $user->getId()
        ], 200);
    }






}
