<?php

declare(strict_types=1);

namespace App\Core\Application\Query\User\GetUser;

use App\App\UI\API\Controller\User\GetUser\GetUserResponse;
use App\Core\Application\Bus\QueryHandler;
use App\Core\Domain\Model\Repository\UserRepository;
use App\Core\Domain\Model\VO\User\UserId;

final readonly class GetUserHandler implements QueryHandler
{
    public function __construct(private UserRepository $repository)
    {
    }

    public function __invoke(GetUserQuery $query): GetUserResponse
    {
        $user = $this->repository->findOneOrFail(new UserId($query->id));

        return GetUserResponse::from($user);
    }
}
