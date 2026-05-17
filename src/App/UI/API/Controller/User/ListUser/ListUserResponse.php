<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\User\ListUser;

use App\App\UI\API\Controller\User\GetUser\GetUserResponse;
use App\Core\Domain\Model\Aggregate\User;
use App\Shared\Domain\Pagination\PaginatedResult;

final readonly class ListUserResponse
{
    /**
     * @param list<GetUserResponse> $items
     */
    public function __construct(
        public array $items,
        public int $totalItems,
        public int $currentPage,
        public int $itemsPerPage,
        public int $totalPages,
    ) {
    }

    public static function from(PaginatedResult $result): self
    {
        $items = array_map(
            static fn (User $user) => GetUserResponse::from($user),
            array_values(array_filter((array) $result->items, static fn (mixed $item) => $item instanceof User)),
        );

        return new self(
            items: $items,
            totalItems: $result->totalItems,
            currentPage: $result->currentPage,
            itemsPerPage: $result->itemsPerPage,
            totalPages: $result->getTotalPages(),
        );
    }
}
