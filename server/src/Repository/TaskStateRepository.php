<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\TaskState;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class TaskStateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, TaskState::class); }

    public function findDefaultActive(): ?TaskState
    {
        return $this->findOneBy(['default' => true, 'isTrashed' => false]);
    }
}
