<?php

namespace App\Service;

use App\Entity\Development;
use App\Entity\Project;
use App\Entity\Technology;
use App\Repository\DevelopmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Uid\Uuid;

class DevelopmentManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private DevelopmentRepository $repository
    ) {}

    public function create(array $data): Development
    {
        if (!isset($data['id'], $data['projectId'], $data['technologyId'], $data['name'])) {
            throw new \InvalidArgumentException('Faltan campos obligatorios (id, projectId, technologyId, name).');
        }

        $development = new Development();
        $development->setId(Uuid::fromString($data['id']));


        $project = $this->em->getRepository(Project::class)->find($data['projectId']);
        $technology = $this->em->getRepository(Technology::class)->find($data['technologyId']);

        if (!$project || !$technology) {
            throw new NotFoundHttpException('Proyecto o Tecnología no encontrados.');
        }

        $development->setProject($project);
        $development->setTechnology($technology);

        return $this->save($development, $data);
    }

    public function save(Development $development, array $data): Development
    {
        $development->setName($data['name'] ?? $development->getName());
        $development->setDescription($data['description'] ?? $development->getDescription());
        $development->setUrlRepository($data['urlRepository'] ?? $development->getUrlRepository());

        // Si se intenta cambiar el proyecto o tecnología en un update
        if (isset($data['projectId'])) {
            $project = $this->em->getRepository(Project::class)->find($data['projectId']);
            if ($project) $development->setProject($project);
        }

        if (isset($data['technologyId'])) {
            $technology = $this->em->getRepository(Technology::class)->find($data['technologyId']);
            if ($technology) $development->setTechnology($technology);
        }

        $this->em->persist($development);
        $this->em->flush();

        return $development;
    }

    public function delete(Development $development): void
    {
        $this->em->remove($development);
        $this->em->flush();
    }
}
