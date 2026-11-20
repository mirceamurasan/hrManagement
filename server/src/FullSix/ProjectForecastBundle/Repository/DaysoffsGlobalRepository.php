<?php

namespace FullSix\ProjectForecastBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

class DaysoffsGlobalRepository extends EntityRepository {

    public function findAllDaysoffsBetweenTwoDates($date_deb, $date_fin) {
        return $this->getEntityManager()
            ->createQuery('SELECT   d
                           FROM     FullSix\ProjectForecastBundle\Entity\DaysoffsGlobal d
                           WHERE    d.DaysoffsGlobalDate >= ?1
                           AND      d.DaysoffsGlobalDate < ?2')
            ->setParameter(1, $date_deb->format('Y/m/d'))
            ->setParameter(2, $date_fin->format('Y/m/d'))
            ->getResult();
    }

}
