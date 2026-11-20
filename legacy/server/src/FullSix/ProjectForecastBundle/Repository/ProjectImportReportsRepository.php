<?php

namespace FullSix\ProjectForecastBundle\Repository;

use Doctrine\ORM\EntityRepository;
use FullSix\ProjectForecastBundle\Entity\Tasks;

class ProjectImportReportsRepository extends EntityRepository {

    /**
     * Gets the latest ProjectImportReport
     *
     * @return mixed|null
     */
    public function findLatest() {
        $qb = $this->createQueryBuilder('pir')
            ->add('orderBy', 'pir.importDate DESC')
            ->setMaxResults(1)
            ->getQuery();

        try {
            return $qb->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

}