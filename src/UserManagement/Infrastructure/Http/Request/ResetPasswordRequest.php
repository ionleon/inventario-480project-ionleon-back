<?php

namespace App\UserManagement\Infrastructure\Http\Request;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class ResetPasswordRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 8)]
        public string $newPassword,
    ) {}

    public static function fromRequest(Request $request): self
    {
        $data = json_decode($request->getContent(), true);

        return new self(
            newPassword: $data['new_password'] ?? '',
        );
    }
}
