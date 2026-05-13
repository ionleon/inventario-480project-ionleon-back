<?php

namespace App\ProjectManagement\Application\DeleteDevelopment;

use App\ProjectManagement\Domain\Development\DevelopmentRepositoryInterface;

final class DeleteDevelopmentHandler
{
    public function __construct(
        private readonly DevelopmentRepositoryInterface $developmentRepository,
    ) {}

    public function handle(DeleteDevelopmentCommand $command): void
    {
        $development = $this->developmentRepository->findById($command->developmentId);

        if (!$development) {
            throw new \DomainException('Development not found');
        }

        $this->developmentRepository->delete($development);
    }
}
