<?php

namespace FullSix\ProjectForecastBundle\Repository;

use Doctrine\ORM\EntityRepository;

class CustomersRepository extends EntityRepository {

    /* Selects the clients that are active and have not been merged before with another client,
     without selecting the current Client.*/
    public function getActiveClientsForMerge($sourceId)
    {
        $qb = $this->createQueryBuilder('c')
            ->where('c.isTrashed = false')
            ->andWhere('c.mergedWith IS null')
            ->andWhere('c.id != :sourceId')
            ->setParameter('sourceId', $sourceId)
            ->getQuery()
            ->getResult()
            ;

        return $qb;
    }

    /**
     * Returns a client based on it's sapId
     *
     * @param string $sapId
     *
     * @return array
     */
    public function findCustomerBySapId($sapId) {
        $qb = $this->createQueryBuilder('c')
            ->innerJoin('c.sapids', 'csi')
            ->where('csi.sapid = :sapid')
            ->setParameter('sapid', $sapId)
            ->andWhere('c.mergedWith IS NULL')
            ->setMaxResults(1)
            ->getQuery();

        try {
            return $qb->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }



}
