<?php

namespace App\UserManagement\Infrastructure\Http\Request;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final class ChangePasswordRequest
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $oldPassword,

        #[Assert\NotBlank]
        #[Assert\Length(min: 8)]
        public readonly string $newPassword,
    ) {}

    public static function fromRequest(Request $request): self
    {
        $data = json_decode($request->getContent(), true);

        return new self(
            oldPassword: $data['old_password'] ?? '',
            newPassword: $data['new_password'] ?? '',
        );
    }
}
