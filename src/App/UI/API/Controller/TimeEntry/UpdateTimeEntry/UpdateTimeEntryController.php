<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\TimeEntry\UpdateTimeEntry;

use App\App\Auth\Domain\Service\SecurityTokenExtractorInterface;
use App\Core\Application\Bus\CommandBus;
use App\Core\Application\Command\TimeEntry\UpdateTimeEntry\UpdateTimeEntryCommand;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'TimeEntry')]
final class UpdateTimeEntryController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly SecurityTokenExtractorInterface $securityTokenExtractor,
    ) {
    }

    #[Route(path: '/time-entries/{id}', methods: ['PUT'])]
    public function __invoke(string $id, #[MapRequestPayload] UpdateTimeEntryRequest $request): Response
    {
        $this->commandBus->dispatch(new UpdateTimeEntryCommand(
            securityToken: ($this->securityTokenExtractor)(),
            id: $id,
            date: $request->date,
            hours: (string) $request->hours,
            description: $request->description,
        ));

        return new Response(status: Response::HTTP_OK);
    }
}
