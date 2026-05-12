<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Client;
use App\Entity\Project;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Uuid;

class ProjectManager
{
    public function __construct(
        private  EntityManagerInterface $entityManager,
        private ClientRepository $clientRepository
    ) {}

    /**
     * @throws Exception
     */
    public function create(array $data, ?Client $client = null) : Project
    {
        if (!isset($data['name'])) {
            throw new \InvalidArgumentException('Project name cannot be empty.');
        }

        $id = Uuid::fromString($data['id']);

        if (!$client && isset($data['client_id'])){
            $client = $this->clientRepository->find($data['client_id']);
        }

        if (!$client) {
            throw new NotFoundHttpException('Client not found.');
        }

        $project = new Project();
        $project->setId($id);
        $project->setClient($client);

        return $this->save($project, $data);
    }

    public function save(Project $project, array $data) : Project
    {

        if (isset($data['name'])) {
            $project->setName($data['name']);
        }

        if (isset($data['description'])) {
            $project->setDescription($data['description']);
        }

        if (array_key_exists('start_date', $data)) {
            $project->setStartedAt($this->normalizeStartDate($data['start_date']));
        }

        $client = null;

        if (isset($data['client_id'])){
            $client = $this->clientRepository->find($data['client_id']);
        }

        if ($client) {
            $project->setClient($client);
        }

        if (isset($data['is_active'])) {
            $project->setIsActive($data['is_active']);
        } elseif ($project->isActive() === null) {
            $project->setIsActive(true);
        }

        $this->entityManager->persist($project);
        $this->entityManager->flush();

        return $project;
    }

    public function delete(Project $project) : void
    {
        $this->entityManager->remove($project);
        $this->entityManager->flush();
    }

    public function deactivate(Project $project) : void
    {
        $project->setIsActive(false);
        $this->entityManager->flush();
    }

    private function normalizeStartDate(mixed $startDate): ?\DateTime
    {
        if ($startDate === null || $startDate === '') {
            return null;
        }

        if ($startDate instanceof \DateTime) {
            return $startDate;
        }

        if ($startDate instanceof \DateTimeInterface) {
            return \DateTime::createFromInterface($startDate);
        }

        if (!is_string($startDate)) {
            throw new \InvalidArgumentException('Invalid start_date value.');
        }

        try {
            return new \DateTime($startDate);
        } catch (\Exception $exception) {
            throw new \InvalidArgumentException('Invalid start_date format.', 0, $exception);
        }
    }

}
