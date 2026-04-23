<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Project;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Serializer\SerializerInterface;

class ProjectManager
{
    public function __construct(
        private  EntityManagerInterface $entityManager,
        private ClientRepository $clientRepository
    ) {}

    /**
     * @throws Exception
     */
    public function create(Project $project, ?string $clientId) : Project
    {


        if (!$clientId) {
            throw new \InvalidArgumentException("Client ID is required", 400);
        }

        $client = $this->clientRepository->find($clientId);
        if(!$client){
            throw new Exception("Client not found with ID: $clientId", 404);
        }
        $project->setClient($client);

        if ($project->isActive() === null){
            $project->setIsActive(true);
        }

        $this->entityManager->persist($project);
        $this->entityManager->flush();

        return $project;
    }

    public function update(Project $project) : void
    {
        $this->entityManager->flush();
    }

    public function remove(Project $project) : void
    {
        $this->entityManager->remove($project);
        $this->entityManager->flush();
    }

    public function deactivate(Project $project) : void
    {
        $project->setIsActive(false);
        $this->entityManager->flush();
    }

}
