<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\TimeEntry\CreateTimeEntry;

use App\App\Auth\Domain\Service\SecurityTokenExtractorInterface;
use App\Core\Application\Bus\CommandBus;
use App\Core\Application\Command\TimeEntry\CreateTimeEntry\CreateTimeEntryCommand;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[OA\Tag(name: 'TimeEntry')]
final class CreateTimeEntryController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly SecurityTokenExtractorInterface $securityTokenExtractor,
    ) {}

    #[Route(path: '/time-entries', methods: ['POST'])]
    public function __invoke(#[MapRequestPayload] CreateTimeEntryRequest $request): Response
    {
        $this->commandBus->dispatch(new CreateTimeEntryCommand(
            securityToken: ($this->securityTokenExtractor)(),
            id: $request->id ?? Uuid::v4()->toRfc4122(),
            projectId: $request->projectId,
            userId: $request->userId,
            date: $request->date,
            hours: (string) $request->hours,
            description: $request->description,
        ));

        return new Response(status: Response::HTTP_CREATED);
    }
}
