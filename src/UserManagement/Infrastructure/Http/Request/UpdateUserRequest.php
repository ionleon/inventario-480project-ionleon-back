<?php

namespace App\UserManagement\Infrastructure\Http\Request;

use App\Shared\Domain\Enum\SystemRole;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
final readonly class UpdateUserRequest
{
    public function __construct(
        #[Assert\Email]
        public ?string     $email = null,

        #[Assert\Length(min: 3)]
        public ?string     $name = null,

        public ?string     $surname = null,

        public ?SystemRole $role = null,

        public ?bool       $isActive = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        $data = json_decode($request->getContent(), true);

        return new self(
            email:    $data['email']     ?? null,
            name:     $data['name']      ?? null,
            surname:  $data['surname']   ?? null,
            role:     isset($data['role'])
                ? SystemRole::from($data['role'])
                : null,
            isActive: $data['is_active'] ?? null,
        );
    }
}
