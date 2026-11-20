<?php

namespace FullSix\ProjectForecastBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

class VacationsRepository extends EntityRepository
{

    public function getVacationsByWorkersInTimePeriod($workerIds, $startDate, $endDate = null)
    {
        if (!$endDate) {
            $endDate = clone $startDate;
            $endDate->modify("+6 days");
        }

        // workerIds can be an array with 1 null element. if this is the case show all vacations
        $workerClause = (null === reset($workerIds)) ? '' : (reset($workerIds) == 0 ? 'AND w.id is NULL' : 'AND w.id IN (:workerIds)') ;
        $query = $this->getEntityManager()
                        ->createQuery('
                                        SELECT      v
                                        FROM        FullSixProjectForecastBundle:Workers w
                                        JOIN        FullSixProjectForecastBundle:Contracts c
                                        WITH        c.idworker = w.id
                                        JOIN        FullSixProjectForecastBundle:Periods p
                                        WITH        p.idcontract = c.id
                                        JOIN        FullSixProjectForecastBundle:Vacations v
                                        WITH        v.vacationperiods = p.id
                                        WHERE       v.isTrashed = 0 '
                                        . $workerClause .
                                        ' AND         (
                                                        (v.startdate <= :startDate AND v.enddate >= :startDate) OR
                                                        (v.startdate >= :startDate AND v.enddate <= :endDate) OR
                                                        (v.startdate <= :endDate AND v.enddate >= :endDate)
                                                    )
                                        ORDER BY    v.startdate ASC,
                                                    v.enddate ASC
                                        ')
                        ->setParameter(':startDate', $startDate->format('Y-m-d'))
                        ->setParameter(':endDate', $endDate->format('Y-m-d'));
        if (reset($workerIds)) {
            $query->setParameter(':workerIds', $workerIds);
        }

        return $query->getResult();
    }

    /**
     * Get all vacations containing
     *
     * @param string $date
     *
     * @return array
     */
    public function findVacationsContaining($date) {
        $qb = $this->createQueryBuilder('v')
                   ->where('v.startdate <= :date')
                   ->andWhere('v.enddate >= :date')
                   ->setParameter('date', $date)
                   ->getQuery()
                   ->getResult();

        return $qb;
    }
}
