<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\User\ChangePassword;

use App\Core\Domain\Exception\User\UserNotFoundException;
use App\Core\Domain\Model\Repository\UserRepository;
use App\Core\Domain\Model\VO\Common\Password;
use App\Core\Domain\Model\VO\User\UserId;
use InvalidArgumentException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class ChangePasswordService implements ChangePasswordServiceInterface
{
    public function __construct(
        private UserRepository $repository,
        private UserPasswordHasherInterface $passwordHasher,
    ) {}

    /** @throws UserNotFoundException */
    public function __invoke(UserId $id, string $oldPassword, string $newPassword): void
    {
        $user = $this->repository->findOneOrFail($id);

        if (!$this->passwordHasher->isPasswordValid($user, $oldPassword)) {
            throw new InvalidArgumentException('Current password is not correct.');
        }

        $hashed = $this->passwordHasher->hashPassword($user, $newPassword);
        $user->changePassword(new Password($hashed));
    }
}
