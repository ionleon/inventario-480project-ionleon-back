<?php

namespace App\Service;

use App\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;

class ProjectManager
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    )
    {}

    public function create(Project $project) : Project
    {
        $project->setIsActive(true);

        $this->entityManager->persist($project);
        $this->entityManager->flush();

        return $project;
    }

    public function update(Project $project) : void
    {

        $this->entityManager->flush();
    }

    public function deactivate(Project $project) : void
    {
        $project->setIsActive(false);
        $this->entityManager->flush();
    }

}
