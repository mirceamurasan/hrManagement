<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Project;
use App\Entity\ProjectTask;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class ProjectTaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, ProjectTask::class); }

    /** @return list<ProjectTask> */
    public function findActiveByProject(Project $project): array
    {
        return $this->createQueryBuilder('task')
            ->addSelect('state')
            ->join('task.state', 'state')
            ->andWhere('task.project = :project')
            ->andWhere('task.isTrashed = false')
            ->orderBy('task.sortOrder', 'ASC')
            ->addOrderBy('task.startDate', 'ASC')
            ->setParameter('project', $project)
            ->getQuery()
            ->getResult();
    }

    public function nextOrderForProject(Project $project): int
    {
        return ((int) $this->createQueryBuilder('task')
            ->select('MAX(task.sortOrder)')
            ->andWhere('task.project = :project')
            ->setParameter('project', $project)
            ->getQuery()
            ->getSingleScalarResult()) + 1;
    }
}
