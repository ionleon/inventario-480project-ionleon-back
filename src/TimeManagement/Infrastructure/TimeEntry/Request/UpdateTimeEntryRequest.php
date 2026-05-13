<?php

namespace App\TimeManagement\Infrastructure\TimeEntry\Request;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class UpdateTimeEntryRequest
{
    public function __construct(
        public ?string $date = null,

        #[Assert\Positive]
        public ?float $hour = null,

        #[Assert\Length(max: 150)]
        public ?string $comment = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        $data = json_decode($request->getContent(), true);

        return new self(
            date:    $data['date']    ?? null,
            hour:    $data['hour']    ?? null,
            comment: $data['comment'] ?? null,
        );
    }
}
