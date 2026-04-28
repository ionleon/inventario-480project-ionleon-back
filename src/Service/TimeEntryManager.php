<?php

namespace App\Service;


use App\Entity\ProjectUser;
use App\Entity\TimeEntry;
use App\Repository\TimeEntryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints\Time;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TimeEntryManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private TimeEntryRepository $repository,
        private ValidatorInterface $validator
    ) {}


    public function create(array $data): TimeEntry
    {
        if (!isset($data['id'], $data['projectUserId'], $data['date'], $data['hour'])) {
            throw new \InvalidArgumentException('Missing mandatory fields (id, projectUser, date, hour)');
        }

        $timeEntry = new TimeEntry();
        $timeEntry->setId(Uuid::fromString($data['id']));

        $projectUser = $this->em->getRepository(ProjectUser::class)->find($data['projectUserId']);
        if (!$projectUser) {
            throw new NotFoundHttpException('Project-User relation does not exist.');
        }

        $timeEntry->setProjectUser($projectUser);

        return $this->save($timeEntry, $data);
    }

    /**
     * @throws \Exception
     */
    public function save(TimeEntry $timeEntry, array $data): TimeEntry
    {
        if (isset($data['date'])) {
            $timeEntry->setDate(new \DateTime($data['date']));
        }

        $timeEntry->setHour($data['hour'] ?? $timeEntry->getHour());
        $timeEntry->setComment($data['comment'] ?? $timeEntry->getComment());

        $errors = $this->validator->validate($timeEntry);
        if (count($errors) > 0) {
            throw new \InvalidArgumentException((string) $errors);
        }

        $this->em->persist($timeEntry);
        $this->em->flush();

        return $timeEntry;
    }

    public function delete(TimeEntry $timeEntry): void
    {
        $this->em->remove($timeEntry);
        $this->em->flush();
    }

}
