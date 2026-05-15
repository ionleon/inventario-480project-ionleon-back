<?php

namespace App\UserManagement\Infrastructure\Http\Request;

use App\Shared\Domain\Enum\SystemRole;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
final readonly class CreateUserRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string     $id,

        #[Assert\NotBlank]
        #[Assert\Email]
        public string     $email,

        #[Assert\NotBlank]
        #[Assert\Length(min: 8)]
        public string     $password,

        #[Assert\NotBlank]
        #[Assert\Length(min: 3)]
        public string     $name,

        #[Assert\NotBlank]
        public string     $surname,

        #[Assert\Type(SystemRole::class)]
        public SystemRole $role = SystemRole::EMPLOYEE,
    ) {}

    public static function fromRequest(Request $request): self
    {
        $data = json_decode($request->getContent(), true);

        return new self(
            id:       $data['id']       ?? '',
            email:    $data['email']    ?? '',
            password: $data['password'] ?? '',
            name:     $data['name']     ?? '',
            surname:  $data['surname']  ?? '',
            role:     isset($data['role'])
                ? SystemRole::from($data['role'])
                : SystemRole::EMPLOYEE,
        );
    }
}
