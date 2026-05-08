<?php

namespace App\ProjectManagement\Domain\Project;

use App\Entity\AppUser;
use App\Entity\Client;
use Doctrine\ORM\QueryBuilder;

interface ProjectRepositoryInterface
{

    public function findByFilters(ProjectFilters $filters) : array;
    public function findByClient(int $clientId): array;
    public function findByUser(int $userId): array;

}
