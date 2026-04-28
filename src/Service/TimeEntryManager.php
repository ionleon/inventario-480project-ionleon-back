<?php

namespace App\Service;

use App\Entity\TimeEntry;
use App\Repository\TimeEntryRepository;
use Doctrine\ORM\EntityManagerInterface;

class TimeEntryManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private TimeEntryRepository $repository
    ) {}

}
