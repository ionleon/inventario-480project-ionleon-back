<?php

namespace App\TimeManagement\Infrastructure\TimeEntry\Request;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateTimeEntryRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $id,

        #[Assert\NotBlank]
        public string $date,

        #[Assert\NotBlank]
        #[Assert\Positive]
        public float $hour,

        #[Assert\Length(max: 150)]
        public ?string $comment = null,

        #[Assert\Uuid]
        public ?string $projectUserId = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        $data = json_decode($request->getContent(), true);

        return new self(
            id:            $data['id']             ?? '',
            date:          $data['date']           ?? '',
            hour:          $data['hour']           ?? 0,
            comment:       $data['comment']        ?? null,
            projectUserId: $data['project_user_id'] ?? null,
        );
    }
}
