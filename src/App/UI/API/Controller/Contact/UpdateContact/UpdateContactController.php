<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\Contact\UpdateContact;

use App\App\Auth\Domain\Service\SecurityTokenExtractorInterface;
use App\Core\Application\Bus\CommandBus;
use App\Core\Application\Command\Contact\UpdateContact\UpdateContactCommand;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Contact')]
final class UpdateContactController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly SecurityTokenExtractorInterface $securityTokenExtractor,
    ) {}

    #[Route(path: '/clients/{clientId}/contacts/{id}', methods: ['PATCH'])]
    public function __invoke(string $id, #[MapRequestPayload] UpdateContactRequest $request): Response
    {
        $this->commandBus->dispatch(new UpdateContactCommand(
            securityToken: ($this->securityTokenExtractor)(),
            id: $id,
            fullName: $request->fullName,
            email: $request->email,
            phoneNumber: $request->phoneNumber,
            note: $request->note,
        ));

        return new Response(status: Response::HTTP_OK);
    }
}
