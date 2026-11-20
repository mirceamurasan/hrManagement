<?php

namespace FullSix\ProjectForecastBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Query\ResultSetMapping;

class PeriodsRepository extends EntityRepository
{

    /**
     * Get all periods starting on a specific day
     *
     * @param string $startDate
     * @return array
     */
    public function findPeriodsStartingOn($startDate)
    {
        $qb = $this->createQueryBuilder('p')
                ->where('p.startdate LIKE :startdate')
                ->setParameter('startdate', '%' . $startDate . '%')
                ->getQuery()
                ->getResult();
        return $qb;
    }

    /**
     * Get all periods containing a date, matching an id
     *
     * @param string $date
     * @param int $id
     * @param boolean sp : if the period is service provider or not
     *
     * @return array
     */
    public function findActivePeriodsContainingDateByJobAndSP($day, $idjob, $sp = false)
    {
     /*   $rsm = new ResultSetMapping();
        $rsm->addEntityResult('FullSix\ProjectForecastBundle\Entity\Periods', 'p');
        $rsm->addFieldResult('p', 'id', 'id');
        $rsm->addFieldResult('p', 'idJob', 'idjob');
        $rsm->addFieldResult('p', 'startdate', 'startdate');
        $rsm->addFieldResult('p', 'enddate', 'enddate');
        $rsm->addFieldResult('p', 'tjm', 'tjm');
        $rsm->addFieldResult('p', 'isTrashed', 'isTrashed');
        $rsm->addFieldResult('p', 'serviceProvider', 'serviceprovider');

        $q = $this->getEntityManager()->createNativeQuery('
            SELECT p.id, p.idjob, p.startdate, p.enddate, p.tjm, p.isTrashed, p.serviceProvider
            FROM Periods p
            WHERE p.idjob = :idjob
            AND DATE(p.startdate) <= :date
            AND DATE(p.enddate) >= :date
            AND p.isTrashed = :trashed
        ', $rsm);
        $q->setParameter('idjob', $idjob)
          ->setParameter('date', $date)
          ->setParameter('trashed', false);

        return $q->getResult();*/


        return $this->getEntityManager()
            ->createQuery('
                    SELECT      p
                    FROM        FullSix\ProjectForecastBundle\Entity\Periods p
                    JOIN        p.idcontract c
                    JOIN        c.idjob j
                    WHERE       p.isTrashed = 0
                    AND         (p.startdate <= :date AND
                                    (p.enddate  IS NULL OR p.enddate >= :date))
                    '.'AND c.idjob  = :idjob
                    AND p.serviceprovider = :sp')
            ->setParameter(':date', $day)
            ->setParameter(':idjob', $idjob)
            ->setParameter(':sp', $sp)
            ->getResult();

    }

    public function findPeriodsMatchingJobsInTimePeriod($jobs, $startDate, $endDate)
    {
        return $this->getEntityManager()
                        ->createQuery('
                    SELECT      p
                    FROM        FullSix\ProjectForecastBundle\Entity\Periods p
                    JOIN        p.idcontract c
                    JOIN        c.idworker w
                    JOIN        c.idjob j
                    WHERE       p.isTrashed = 0
                    AND         (
                                (p.startdate <= :startDate AND p.enddate >= :startDate) OR
                                (p.startdate >= :startDate AND p.enddate <= :endDate) OR
                                (p.startdate <= :endDate AND p.enddate >= :endDate) OR
                                (p.startdate <= :endDate AND p.enddate  IS NULL )
                    )
                   ' . (count($jobs) ? 'AND c.idjob IN (' . implode(',', $jobs) . ')' . (in_array(0, $jobs) ? ' OR c.idjob IS NULL' : '') : '') . '
                    ORDER BY    j.jobslabel DESC
        ')
                        ->setParameter(':startDate', $startDate->format('Y-m-d'))
                        ->setParameter(':endDate', $endDate->format('Y-m-d'))
                        ->getResult();
    }

}