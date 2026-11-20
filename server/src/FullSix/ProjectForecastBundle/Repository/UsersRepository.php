<?php

namespace FullSix\ProjectForecastBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

class UsersRepository extends EntityRepository {

    public function getUsersOrderByJobBetweenDate($start_date, $end_date, Array $jobs = array())
    {

        return $this->getEntityManager()
            ->createQuery('
                            SELECT      u
                            FROM        FullSix\ProjectForecastBundle\Entity\Users u
                            LEFT JOIN	FullSix\ProjectForecastBundle\Entity\Jobs j
                            WITH		u.useridjobs = j.id
                            WHERE		((u.usersbeginat <= ?1
                            AND			(u.usersendat >= ?1 OR u.usersendat IS NULL))
                            OR			(u.usersbeginat > ?1
                            AND			u.usersbeginat < ?2))
                            ' . (count($jobs) ? 'AND j.id IN (' . implode(',', $jobs) . ')' . (in_array(0, $jobs) ? ' OR j.id IS NULL' : '') : '') . '
                            ORDER BY    j.jobslabel DESC, u.userslastname ASC, u.usersfirstname ASC, u.username ASC
                ')
            ->setParameter(1, $start_date)
            ->setParameter(2, $end_date)
            ->getResult();
    }

    public function getUsersByHasCharge($userid, $limit = 10)
    {
        $datelimit = new \DateTime();
        $datelimit->sub(new \DateInterval('P'.$limit.'D'));

        return $this->getEntityManager()
            ->createQuery('
                            SELECT      u
                            FROM        FullSix\ProjectForecastBundle\Entity\Users u, FullSix\ProjectForecastBundle\Entity\Projects p
                            JOIN        p.projectscotechoperational po
                            JOIN        p.projectscotechsupervisor ps
                            WHERE       (po = u.id OR ps = u.id OR u.id = ?2)
                            AND         p.isTrashed = 0
                            AND         p.projectlasttasktdate > ?1
                            ORDER BY    u.userslastname ASC
                        ')
            ->setParameter(1, $datelimit)
            ->setParameter(2, $userid)
            ->getResult();
    }

    /* Selects the users that have not been merged before with another user,
     without selecting the current user.*/
    public function getUsersForMerge($sourceId)
    {
        $qb = $this->createQueryBuilder('u')
            ->where('u.mergedWith IS null')
            ->andWhere('u.id != :sourceId')
            ->setParameter('sourceId', $sourceId)
            ->getQuery()
            ->getResult()
            ;

        return $qb;
    }

    public function mergeUsers($destinationId, $sourceId)
    {
         $qb = $this->createQueryBuilder('u')
            ->update()
            ->where('u.id = :destinationId')
            ->set('u.mergedWith', ':sourceId')
            ->setParameter('destinationId', $destinationId)
            ->setParameter('sourceId', $sourceId)
            ->getQuery()
            ->execute()
            ;
    }

    public function getUsersForMergeWhichAreNotWorkers($sourceId)
    {
        $qb = $this->createQueryBuilder('u')
            ->leftJoin('u.worker', 'w')
            ->where('u.mergedWith IS null')
            ->andWhere('u.id != :sourceId')
            ->andWhere('w.id IS null')
            ->setParameter('sourceId', $sourceId)
            ->getQuery()
            ->getResult()
            ;

        return $qb;
    }
}