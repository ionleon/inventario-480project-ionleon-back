<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\User\ChangePassword;

use App\App\Auth\Domain\Service\SecurityTokenExtractorInterface;
use App\Core\Application\Bus\CommandBus;
use App\Core\Application\Command\User\ChangePassword\ChangePasswordCommand;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'User')]
final class ChangePasswordController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly SecurityTokenExtractorInterface $securityTokenExtractor,
    ) {}

    #[Route(path: '/users/{id}/password-change', methods: ['PUT'])]
    public function __invoke(string $id, #[MapRequestPayload] ChangePasswordRequest $request): Response
    {
        $this->commandBus->dispatch(new ChangePasswordCommand(
            securityToken: ($this->securityTokenExtractor)(),
            id: $id,
            oldPassword: $request->oldPassword,
            newPassword: $request->newPassword,
        ));

        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}
