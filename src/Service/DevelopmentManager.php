<?php

namespace App\Service;

use App\Entity\Development;
use App\Entity\Project;
use App\Entity\Technology;
use App\Repository\DevelopmentRepository;
use App\Repository\ProjectRepository;
use App\Repository\TechnologyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Uid\Uuid;

class DevelopmentManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private DevelopmentRepository $devRepository,
        private TechnologyRepository $technologyRepository,
        private ProjectRepository $projectRepository,
    ) {}

    public function findAllByProject(Project $project): array
    {
        return $this->devRepository->findBy(['project' => $project]);
    }

    public function create(Project $project, array $data): Development
    {
        if (!isset($data['id'], $data['technologyId'], $data['name'])) {
            throw new \InvalidArgumentException('Faltan campos obligatorios (id, technologyId, name).');
        }

        $development = new Development();
        $development->setId(Uuid::fromString($data['id']));
        $development->setProject($project);

        return $this->save($development, $data);
    }

    public function save(Development $development, array $data): Development
    {
        $development->setName($data['name'] ?? $development->getName());
        $development->setDescription($data['description'] ?? $development->getDescription());
        $development->setUrlRepository($data['urlRepository'] ?? $development->getUrlRepository());


        if (isset($data['technologyId'])) {
            $technology = $this->technologyRepository->find($data['technologyId']);
            if (!$technology) {
                throw new NotFoundHttpException('Proyecto o Tecnología no encontrados.');
            }
            $development->setTechnology($technology);
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
