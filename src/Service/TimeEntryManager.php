<?php

namespace App\Service;


use App\Entity\AppUser;
use App\Entity\Project;
use App\Entity\ProjectUser;
use App\Entity\TimeEntry;
use App\Repository\ProjectUserRepository;
use App\Repository\TimeEntryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints\Time;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TimeEntryManager
{


    public function __construct(
        private EntityManagerInterface $em,
        private TimeEntryRepository $repository,
        private ValidatorInterface $validator,
        private ProjectUserRepository $puRepository,
        private Security $security,
    ) {}


    public function getAllByProjects(Project $project, ?AppUser $user = null) : array
    {
        return $this->repository->findByProjectAndUser($project, $user);
    }

    /**
     * @throws Exception
     */
    public function create(array $data, ?Project $project = null): TimeEntry
    {
        if (!isset($data['id'], $data['date'], $data['hour'])) {
            throw new \InvalidArgumentException('Missing mandatory fields (id, date, hour)');
        }

        $timeEntry = new TimeEntry();
        try {
            $timeEntry->setId(Uuid::fromString($data['id']));
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException('UUID format invalid.');
        }

        $projectUser = null;
        if ($project) {
            $projectUser = $this->puRepository->findOneBy([
                'project' => $project,
                'user' => $this->security->getUser()
            ]);
        } elseif (isset($data['projectUserId'])) {
            $projectUser = $this->puRepository->find($data['projectUserId']);
        }

        if (!$projectUser) {
            throw new NotFoundHttpException('Project-User relation does not exist or user is not assigned to this project.');
        }

        $timeEntry->setProjectUser($projectUser);

        return $this->save($timeEntry, $data);
    }

    /**
     * @throws Exception
     */
    public function save(TimeEntry $timeEntry, array $data, bool $flush = true): TimeEntry
    {
        if (isset($data['date'])) {
            try {
                $timeEntry->setDate(new \DateTime($data['date']));
            } catch (\Exception $e) {
                throw new \InvalidArgumentException('Date format invalid. Use YYYY-MM-DD.');
            }
        }

        $timeEntry->setHour($data['hour'] ?? $timeEntry->getHour());
        $timeEntry->setComment($data['comment'] ?? $timeEntry->getComment());

        $errors = $this->validator->validate($timeEntry);
        if (count($errors) > 0) {
            throw new \InvalidArgumentException((string) $errors);
        }

        $this->em->persist($timeEntry);

        if ($flush) {
            $this->em->flush();
        }

        return $timeEntry;
    }

    public function delete(TimeEntry $timeEntry): void
    {
        $this->em->remove($timeEntry);
        $this->em->flush();
    }

}
