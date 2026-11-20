<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Job;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class JobRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, Job::class); }

    /** @return list<Job> */
    public function findActive(): array
    {
        return $this->createQueryBuilder('job')
            ->andWhere('job.isTrashed = false')
            ->orderBy('job.label', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
