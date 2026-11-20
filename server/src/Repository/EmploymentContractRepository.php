<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\EmploymentContract;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class EmploymentContractRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, EmploymentContract::class); }

    /** @return list<EmploymentContract> */
    public function findActive(): array
    {
        return $this->createQueryBuilder('contract')
            ->addSelect('worker', 'type', 'employer', 'job')
            ->join('contract.worker', 'worker')
            ->join('contract.type', 'type')
            ->join('contract.employer', 'employer')
            ->join('contract.job', 'job')
            ->andWhere('contract.isTrashed = false')
            ->orderBy('worker.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
