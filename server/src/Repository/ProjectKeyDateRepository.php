<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Project;
use App\Entity\ProjectKeyDate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class ProjectKeyDateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, ProjectKeyDate::class); }
    /** @return list<ProjectKeyDate> */
    public function findByProject(Project $project): array
    {
        return $this->findBy(['project' => $project], ['date' => 'ASC']);
    }
}
