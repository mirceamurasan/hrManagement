<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ContractPeriod;
use App\Entity\EmploymentContract;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class ContractPeriodRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, ContractPeriod::class); }
    /** @return list<ContractPeriod> */
    public function findActiveByContract(EmploymentContract $contract): array
    {
        return $this->createQueryBuilder('period')->andWhere('period.contract = :contract')->andWhere('period.isTrashed = false')->setParameter('contract', $contract)->orderBy('period.startDate', 'DESC')->getQuery()->getResult();
    }

    /** @return list<ContractPeriod> */
    public function findActive(): array
    {
        return $this->createQueryBuilder('period')
            ->addSelect('contract', 'worker')
            ->join('period.contract', 'contract')
            ->join('contract.worker', 'worker')
            ->andWhere('period.isTrashed = false')
            ->andWhere('contract.isTrashed = false')
            ->andWhere('worker.isTrashed = false')
            ->orderBy('worker.name', 'ASC')
            ->addOrderBy('period.startDate', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
