<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\User\DeleteUser;

use App\Core\Domain\Exception\User\UserNotFoundException;
use App\Core\Domain\Model\Repository\UserRepository;
use App\Core\Domain\Model\VO\User\UserId;

final readonly class DeleteUserService implements DeleteUserServiceInterface
{
    public function __construct(private UserRepository $repository)
    {
    }

    /** @throws UserNotFoundException */
    public function __invoke(UserId $id): void
    {
        $user = $this->repository->findOneOrFail($id);

        $this->repository->remove($user);
    }
}
