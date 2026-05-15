<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\User\CreateUser;

use App\Core\Domain\Exception\User\DuplicatedUserEmailException;
use App\Core\Domain\Model\Aggregate\User;
use App\Core\Domain\Model\Repository\UserRepository;
use App\Core\Domain\Model\VO\Common\Email;
use App\Core\Domain\Model\VO\Common\Password;
use App\Core\Domain\Model\VO\User\UserId;
use App\Core\Domain\Model\VO\User\UserName;
use App\Core\Domain\Model\VO\User\UserSurname;
use App\Shared\Domain\Enum\SystemRole;

final readonly class CreateUserService implements CreateUserServiceInterface
{
    public function __construct(private UserRepository $repository) {}

    /** @throws DuplicatedUserEmailException */
    public function __invoke(
        UserId $id,
        Email $email,
        UserName $name,
        UserSurname $surname,
        Password $password,
        SystemRole $role,
    ): User {
        if ($this->repository->findOneByEmail($email) !== null) {
            throw new DuplicatedUserEmailException((string) $email);
        }

        $user = User::create(
            id: $id,
            email: $email,
            name: $name,
            surname: $surname,
            password: $password,
            role: $role,
        );

        $this->repository->add($user);

        return $user;
    }
}
