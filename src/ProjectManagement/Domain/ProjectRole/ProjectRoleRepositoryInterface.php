<?php

namespace App\ProjectManagement\Domain\ProjectRole;

use App\Entity\ProjectRole;

interface ProjectRoleRepositoryInterface
{
    public function findById(int $id): ProjectRole;


}
