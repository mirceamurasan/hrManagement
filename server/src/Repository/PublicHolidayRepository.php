<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\PublicHoliday;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class PublicHolidayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PublicHoliday::class);
    }

    /** @return list<PublicHoliday> */
    public function findBetween(DateTimeImmutable $start, DateTimeImmutable $end): array
    {
        return $this->createQueryBuilder('holiday')
            ->andWhere('holiday.date >= :start')
            ->andWhere('holiday.date <= :end')
            ->setParameter('start', $start->setTime(0, 0))
            ->setParameter('end', $end->setTime(0, 0))
            ->orderBy('holiday.date', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
