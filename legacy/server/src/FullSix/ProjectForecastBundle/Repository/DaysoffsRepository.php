<?php

namespace FullSix\ProjectForecastBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

class DaysoffsRepository extends EntityRepository {

    public function findDaysoffsByUserIdAndYear($id, $year) {
        $date_deb = new \DateTime('01-01-'.$year);
        $date_fin = new \DateTime('01-01-'.($year+1));
        return $this->getEntityManager()
            ->createQuery('SELECT   d
                           FROM     FullSixProjectForecastBundle:Daysoffs d
                           WHERE    d.daysoffsuserid = ?1
                           AND      d.DaysoffsDate >= ?2
                           AND      d.DaysoffsDate < ?3')
            ->setParameter(1, $id)
            ->setParameter(2, $date_deb->format('Y/m/d'))
            ->setParameter(3, $date_fin->format('Y/m/d'))
            ->getResult();
    }

    public function getYearWithDaysOff($user_id) 
    {
      //SELECT SUM(d.`DaysoffsMorning` + d.`DaysoffsAfternoon`),  DATE_FORMAT(d.`DaysoffsDate`) FROM Daysoffs d WHERE d.`DaysoffsUsersId` = 1 GROUP BY MONTH(d.`DaysoffsDate`) ORDER BY d.`DaysoffsDate`
      
      $q = "SELECT d.DaysoffsDate AS current_year, SUBSTRING(d.DaysoffsDate, 0, 4) AS year, SUM(d.DaysoffsMorning + d.DaysoffsAfternoon) AS days_off  FROM FullSix\ProjectForecastBundle\Entity\Daysoffs d WHERE d.daysoffsuserid = ?1 GROUP BY year ORDER BY d.DaysoffsDate";
      
      return $this->getEntityManager()->createQuery($q)
          ->setParameter(1, $user_id)
          ->getResult();
    }

    public function getMonthWithDaysOff($user_id) 
    {
      //SELECT SUM(d.`DaysoffsMorning` + d.`DaysoffsAfternoon`),  DATE_FORMAT(d.`DaysoffsDate`) FROM Daysoffs d WHERE d.`DaysoffsUsersId` = 1 GROUP BY MONTH(d.`DaysoffsDate`) ORDER BY d.`DaysoffsDate`
      
      $q = "SELECT d.DaysoffsDate AS current_month, SUBSTRING(d.DaysoffsDate, 6, 2) AS month, SUM(d.DaysoffsMorning + d.DaysoffsAfternoon) AS days_off  FROM FullSix\ProjectForecastBundle\Entity\Daysoffs d WHERE d.daysoffsuserid = ?1 GROUP BY month ORDER BY d.DaysoffsDate";
      
      return $this->getEntityManager()->createQuery($q)
          ->setParameter(1, $user_id)
          ->getResult();
    }

    public function getDaysOffForMonth($user_id, $start_date, $stop_date)
    {
      return $this->getEntityManager()
            ->createQuery('SELECT   d
                           FROM     FullSixProjectForecastBundle:Daysoffs d
                           WHERE    d.daysoffsuserid = ?1
                           AND      d.DaysoffsDate >= ?2
                           AND      d.DaysoffsDate < ?3')
            ->setParameter(1, $user_id)
            ->setParameter(2, $start_date)
            ->setParameter(3, $stop_date)
            ->getResult();
    }


    public function findAllDaysoffsBetweenTwoDates($date_deb, $date_fin) {
        return $this->getEntityManager()
            ->createQuery('SELECT   d
                           FROM     FullSixProjectForecastBundle:Daysoffs d
                           WHERE    d.DaysoffsDate >= ?1
                           AND      d.DaysoffsDate < ?2')
            ->setParameter(1, $date_deb->format('Y/m/d'))
            ->setParameter(2, $date_fin->format('Y/m/d'))
            ->getResult();
    }

    public function getAbsence()
    {

        return $this->getEntityManager()
            ->createQuery('SELECT *,
( SELECT (SUM(d2.DaysoffsMorning) + SUM(d2.DaysoffsAfternoon) ) /2
FROM Daysoffs d2
WHERE d2.DaysoffsDate BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 15 DAY)
AND d2.DaysoffsUsersId = d.DaysoffsUsersId ) AS jours
FROM Daysoffs d
WHERE d.DaysoffsDate  BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 15 DAY)
GROUP BY d.DaysoffsUsersId')->getResult();
    }

}
