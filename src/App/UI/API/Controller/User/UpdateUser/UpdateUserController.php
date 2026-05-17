<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\User\UpdateUser;

use App\App\Auth\Domain\Service\SecurityTokenExtractorInterface;
use App\Core\Application\Bus\CommandBus;
use App\Core\Application\Command\User\UpdateUser\UpdateUserCommand;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'User')]
final class UpdateUserController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly SecurityTokenExtractorInterface $securityTokenExtractor,
    ) {
    }

    #[Route(path: '/users/{id}', methods: ['PUT'])]
    public function __invoke(string $id, #[MapRequestPayload] UpdateUserRequest $request): Response
    {
        $this->commandBus->dispatch(new UpdateUserCommand(
            securityToken: ($this->securityTokenExtractor)(),
            id: $id,
            name: $request->name,
            surname: $request->surname,
        ));

        return new Response(status: Response::HTTP_OK);
    }
}
