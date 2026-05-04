<?php

namespace App\Service;


use App\Entity\AppUser;
use App\Entity\Project;
use App\Entity\ProjectUser;
use App\Entity\TimeEntry;
use App\Repository\ProjectRepository;
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
        private ValidatorInterface     $validator,
        private ProjectUserRepository  $puRepository,
        private ProjectRepository      $projectRepository,
        private Security               $security,
    ) {}



    /**
     * @throws Exception
     */
    public function create(array $data, ?Project $project = null, ?AppUser $targetUser = null): TimeEntry
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
        $user = $targetUser ?? $this->security->getUser();

        if (isset($data['project_user_id'])) {
            $projectUser = $this->puRepository->find($data['project_user_id']);
        }

        else {
            if (!$project && isset($data['project_id'])) {
                $project = $this->projectRepository->find($data['project_id']);
            }

            if ($project) {
                $projectUser = $this->puRepository->findOneBy([
                    'project' => $project,
                    'appUser' => $user
                ]);
            }
        }

        if (!$projectUser) {
            throw new NotFoundHttpException('Project-User relation does not exist or user is not assigned to this project.');
        }

        if ($targetUser && $projectUser->getAppUser() !== $targetUser) {
            throw new \LogicException('The assignment does not belong to the user specified in the URL.');
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
