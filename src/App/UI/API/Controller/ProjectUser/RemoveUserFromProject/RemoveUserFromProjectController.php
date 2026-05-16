<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\ProjectUser\RemoveUserFromProject;

use App\App\Auth\Domain\Service\SecurityTokenExtractorInterface;
use App\Core\Application\Bus\CommandBus;
use App\Core\Application\Command\ProjectUser\RemoveUserFromProject\RemoveUserFromProjectCommand;
use App\Core\Domain\Exception\ProjectUser\ProjectUserNotFoundException;
use App\Core\Domain\Model\Repository\ProjectUserRepository;
use App\Core\Domain\Model\VO\Project\ProjectId;
use App\Core\Domain\Model\VO\User\UserId;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'ProjectUser')]
final class RemoveUserFromProjectController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly SecurityTokenExtractorInterface $securityTokenExtractor,
        private readonly ProjectUserRepository $projectUserRepository,
    ) {}

    #[Route(path: '/projects/{id}/users/{userId}', methods: ['DELETE'])]
    public function __invoke(string $id, string $userId): Response
    {
        $projectUser = $this->projectUserRepository->findOneByProjectAndUser(
            new ProjectId($id),
            new UserId($userId),
        );

        if ($projectUser === null) {
            throw new ProjectUserNotFoundException();
        }

        $this->commandBus->dispatch(new RemoveUserFromProjectCommand(
            securityToken: ($this->securityTokenExtractor)(),
            id: (string) $projectUser->id(),
        ));

        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}
