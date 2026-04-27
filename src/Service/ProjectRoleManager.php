<?php

namespace App\Service;

use App\Repository\ProjectRoleRepository;
use Doctrine\ORM\EntityManagerInterface;

class ProjectRoleManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private ProjectRoleRepository $repository
    )
    {}

}
