<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\User\CreateUser;

use App\App\Auth\Domain\Service\SecurityTokenExtractorInterface;
use App\Core\Application\Bus\CommandBus;
use App\Core\Application\Command\User\CreateUser\CreateUserCommand;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'User')]
final class CreateUserController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly SecurityTokenExtractorInterface $securityTokenExtractor,
    ) {}

    #[Route(path: '/users', methods: ['POST'])]
    public function __invoke(#[MapRequestPayload] CreateUserRequest $request): Response
    {
        $this->commandBus->dispatch(new CreateUserCommand(
            securityToken: ($this->securityTokenExtractor)(),
            id: $request->id,
            email: $request->email,
            name: $request->name,
            surname: $request->surname,
            password: $request->password,
            role: $request->role,
        ));

        return new Response(status: Response::HTTP_CREATED);
    }
}
