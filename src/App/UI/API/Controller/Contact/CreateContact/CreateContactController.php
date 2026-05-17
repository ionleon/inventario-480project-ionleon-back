<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\Contact\CreateContact;

use App\App\Auth\Domain\Service\SecurityTokenExtractorInterface;
use App\Core\Application\Bus\CommandBus;
use App\Core\Application\Command\Contact\CreateContact\CreateContactCommand;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Contact')]
final class CreateContactController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly SecurityTokenExtractorInterface $securityTokenExtractor,
    ) {
    }

    #[Route(path: '/clients/{clientId}/contacts', methods: ['POST'])]
    public function __invoke(string $clientId, #[MapRequestPayload] CreateContactRequest $request): Response
    {
        $this->commandBus->dispatch(new CreateContactCommand(
            securityToken: ($this->securityTokenExtractor)(),
            id: $request->id,
            clientId: $clientId,
            fullName: $request->fullName,
            email: $request->email,
            phoneNumber: $request->phoneNumber,
            note: $request->note,
        ));

        return new Response(status: Response::HTTP_CREATED);
    }
}
