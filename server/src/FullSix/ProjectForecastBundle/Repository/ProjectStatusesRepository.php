<?php

namespace FullSix\ProjectForecastBundle\Repository;

use Doctrine\ORM\EntityRepository;
use FullSix\ProjectForecastBundle\Entity\Tasks;

class ProjectStatusesRepository extends EntityRepository {

    /**
     * Gets a ProjectStatus object based on it's label
     *
     * @param string $label
     *
     * @return array
     */
    public function findStatusByLabel($label) {
        $qb = $this->createQueryBuilder('ps')
            ->where('ps.projectstatuseslabel = :label')
            ->setParameter('label', $label)
            ->setMaxResults(1)
            ->getQuery();

        try {
            return $qb->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

}