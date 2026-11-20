<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ProjectTask;
use App\Entity\TaskAllocation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class TaskAllocationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, TaskAllocation::class); }

    /** @return list<TaskAllocation> */
    public function findActiveByTask(ProjectTask $task): array
    {
        return $this->createQueryBuilder('allocation')
            ->addSelect('job', 'provider')
            ->join('allocation.job', 'job')
            ->leftJoin('allocation.provider', 'provider')
            ->andWhere('allocation.task = :task')
            ->andWhere('allocation.isTrashed = false')
            ->orderBy('allocation.volume', 'DESC')
            ->setParameter('task', $task)
            ->getQuery()
            ->getResult();
    }
}
