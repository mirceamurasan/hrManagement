<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ProjectTask;
use App\Entity\TaskAllocation;
use App\Entity\Worker;
use DateTimeImmutable;
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

    /** @return list<TaskAllocation> */
    public function findForWeek(DateTimeImmutable $start, DateTimeImmutable $end, ?Worker $worker = null): array
    {
        $query = $this->createQueryBuilder('allocation')
            ->addSelect('task', 'project', 'job', 'worker', 'provider')
            ->join('allocation.task', 'task')
            ->join('task.project', 'project')
            ->join('allocation.job', 'job')
            ->leftJoin('allocation.worker', 'worker')
            ->leftJoin('allocation.provider', 'provider')
            ->andWhere('allocation.isTrashed = false')
            ->andWhere('task.isTrashed = false')
            ->andWhere('task.startDate <= :end')
            ->andWhere('task.endDate >= :start')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->orderBy('project.name', 'ASC')
            ->addOrderBy('task.startDate', 'ASC');
        if ($worker !== null) {
            $query->andWhere('allocation.worker = :worker')->setParameter('worker', $worker);
        }

        return $query->getQuery()->getResult();
    }
}
