<?php

namespace App\Controller;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\ModelDescriber\Annotations;

use App\Entity\AppUser;
use App\Repository\AppUserRepository;
use App\Service\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Uuid;

use Symfony\Component\Validator\Validator\ValidatorInterface;


/*
 * Cambiar controllers para que utilizen servicios, adaptarlos a arquitectura hexagonal a futuro
 * */


#[Route('/users')]
#[OA\Tag(name: 'Users')]
final class UserController extends AbstractController
{
    public function __construct(
        private AppUserRepository $repository,
        private UserManager $um,
        private UserPasswordHasherInterface $hasher
    ) {}

    #[Route('', name: 'app_user_index', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Devuelve la lista de usuarios',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: AppUser::class))
        )
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function index(Request $request, AppUserRepository $repository): JsonResponse
    {
        $term = $request->query->get('term');
        $role = $request->query->get('role');
        $isActive = $request->query->has('isActive')
                    ? $request->query->getBoolean('isActive')
                    : null;

        $users = $repository->findByFilters($term, $role, $isActive);

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
    #[IsGranted('ROLE_ADMIN')]
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


        $userManager->create($user);

        return $this->json($user, 201, [], ['groups' => 'user:read']);
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/{id}', name: 'app_user_edit', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(
        Uuid  $id,
        AppUserRepository $repository,
        Request $request,
        SerializerInterface $serializer,
        UserManager $userManager,
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
            [
                AbstractNormalizer::OBJECT_TO_POPULATE => $user,
                'groups' => ['user:update']
            ]
        );

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            return $this->json($errors, 400);
        }

        $userManager->update($user);

        return $this->json($user, 200, [], ['groups' => 'user:read']);

    }

    #[Route('/{id}/password-change', name: 'user_password_change', methods: ['PUT'])]
    public function changePassword(AppUser $user, Request $request ): JsonResponse
    {

        if ($user !== $this->getUser()) {
            throw $this->createAccessDeniedException('No puedes cambiar la contraseña de otro usuario.');
        }

        $data = json_decode($request->getContent(), true);

        // Lógica de validación de 'old_password' y 'new_password'
        // ...

        return $this->json(['message' => 'Contraseña actualizada con éxito']);
    }

    #[Route('/{id}/admin-password', name: 'user_admin_password_reset', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function adminPasswordChange(AppUser $user, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        return $this->json(['message' => 'Contraseña reseteada por el administrador']);
    }


    #[Route('/{id}', name: 'app_user_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Uuid $id, AppUserRepository $repository, UserManager $um): JsonResponse
    {
        $user = $repository->find($id);
        if (!$user) return $this->json(['error' => 'User not found'], 404);

        $um->remove($user);

        return $this->json(null, 204);
    }
    #[Route('/{id}', name: 'app_user_deactivate', methods: ['PATCH'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deactivate(
        Uuid $id,
        AppUserRepository $repository,
        UserManager $userManager
    ) : JsonResponse
    {

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
