<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ProjectStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class ProjectStatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjectStatus::class);
    }

    public function findByLabel(string $label): ?ProjectStatus
    {
        return $this->findOneBy(['label' => trim($label)]);
    }
}
