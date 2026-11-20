<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    /** @return list<Project> */
    public function findActive(): array
    {
        return $this->createQueryBuilder('project')
            ->addSelect('customer', 'status')
            ->join('project.customer', 'customer')
            ->leftJoin('project.status', 'status')
            ->andWhere('project.isTrashed = false')
            ->orderBy('project.updatedAt', 'DESC')
            ->addOrderBy('project.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findBySapNumber(string $sapNumber): ?Project
    {
        return $this->findOneBy(['sapNumber' => trim($sapNumber)]);
    }
}
