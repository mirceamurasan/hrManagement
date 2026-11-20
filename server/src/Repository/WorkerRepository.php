<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Worker;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class WorkerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, Worker::class); }

    /** @return list<Worker> */
    public function findActive(): array
    {
        return $this->createQueryBuilder('worker')->andWhere('worker.isTrashed = false')->orderBy('worker.name')->getQuery()->getResult();
    }
}
