<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\User\UpdateUser;

use App\Core\Domain\Exception\User\UserNotFoundException;
use App\Core\Domain\Model\Aggregate\User;
use App\Core\Domain\Model\Repository\UserRepository;
use App\Core\Domain\Model\VO\User\UserId;
use App\Core\Domain\Model\VO\User\UserName;
use App\Core\Domain\Model\VO\User\UserSurname;

final readonly class UpdateUserService implements UpdateUserServiceInterface
{
    public function __construct(private UserRepository $repository)
    {
    }

    /** @throws UserNotFoundException */
    public function __invoke(UserId $id, UserName $name, UserSurname $surname): User
    {
        $user = $this->repository->findOneOrFail($id);

        $user->updateProfile($name, $surname);

        return $user;
    }
}
