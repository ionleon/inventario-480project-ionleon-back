<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\User\ResetPassword;

use App\Core\Domain\Exception\User\UserNotFoundException;
use App\Core\Domain\Model\Repository\UserRepository;
use App\Core\Domain\Model\VO\Common\Password;
use App\Core\Domain\Model\VO\User\UserId;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class ResetPasswordService implements ResetPasswordServiceInterface
{
    public function __construct(
        private UserRepository $repository,
        private UserPasswordHasherInterface $passwordHasher,
    ) {}

    /** @throws UserNotFoundException */
    public function __invoke(UserId $id, string $newPassword): void
    {
        $user = $this->repository->findOneOrFail($id);

        $hashed = $this->passwordHasher->hashPassword($user, $newPassword);
        $user->resetPasswordByAdmin(new Password($hashed));
    }
}
