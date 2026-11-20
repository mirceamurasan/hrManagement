<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\WorkerVacation;
use App\Entity\Worker;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class WorkerVacationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, WorkerVacation::class); }
    /** @return list<WorkerVacation> */
    public function findActive(): array
    {
        return $this->createQueryBuilder('vacation')
            ->addSelect('period', 'contract', 'worker')
            ->join('vacation.period', 'period')
            ->join('period.contract', 'contract')
            ->join('contract.worker', 'worker')
            ->andWhere('vacation.isTrashed = false')
            ->orderBy('worker.name', 'ASC')
            ->addOrderBy('vacation.startDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /** @return list<WorkerVacation> */
    public function findOverlapping(DateTimeImmutable $start, DateTimeImmutable $end, ?Worker $worker = null): array
    {
        $query = $this->createQueryBuilder('vacation')
            ->addSelect('period', 'contract', 'worker')
            ->join('vacation.period', 'period')
            ->join('period.contract', 'contract')
            ->join('contract.worker', 'worker')
            ->andWhere('vacation.isTrashed = false')
            ->andWhere('vacation.startDate <= :end')
            ->andWhere('vacation.endDate >= :start')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->orderBy('worker.name', 'ASC')
            ->addOrderBy('vacation.startDate', 'ASC');
        if ($worker !== null) {
            $query->andWhere('worker = :worker')->setParameter('worker', $worker);
        }

        return $query->getQuery()->getResult();
    }
}
