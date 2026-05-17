<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\ProjectUser\UpdateProjectUser;

use App\App\Auth\Domain\Service\SecurityTokenExtractorInterface;
use App\Core\Application\Bus\CommandBus;
use App\Core\Application\Command\ProjectUser\UpdateProjectUser\UpdateProjectUserCommand;
use App\Core\Domain\Exception\ProjectUser\ProjectUserNotFoundException;
use App\Core\Domain\Model\Repository\ProjectUserRepository;
use App\Core\Domain\Model\VO\Project\ProjectId;
use App\Core\Domain\Model\VO\User\UserId;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'ProjectUser')]
final class UpdateProjectUserController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly SecurityTokenExtractorInterface $securityTokenExtractor,
        private readonly ProjectUserRepository $projectUserRepository,
    ) {
    }

    #[Route(path: '/projects/{id}/users/{userId}', methods: ['PUT'])]
    public function __invoke(string $id, string $userId, #[MapRequestPayload] UpdateProjectUserRequest $request): Response
    {
        $projectUser = $this->projectUserRepository->findOneByProjectAndUser(
            new ProjectId($id),
            new UserId($userId),
        );

        if ($projectUser === null) {
            throw new ProjectUserNotFoundException();
        }

        $this->commandBus->dispatch(new UpdateProjectUserCommand(
            securityToken: ($this->securityTokenExtractor)(),
            id: (string) $projectUser->id(),
            roleId: $request->roleId,
            allocation: $request->allocation,
        ));

        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}
