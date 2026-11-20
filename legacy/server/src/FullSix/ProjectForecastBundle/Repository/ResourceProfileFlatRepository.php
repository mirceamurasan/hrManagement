<?php

namespace FullSix\ProjectForecastBundle\Repository;

use Doctrine\ORM\EntityRepository;
use FullSix\ProjectForecastBundle\Entity\Tasks;


class ResourceProfileFlatRepository extends EntityRepository {

    /*Gets all resourceProfileFlat relationsships between this worker and the following projects. */
    public function getProjectsAssignedToWorker($worker, $projectIds) {
        $qb = $this->createQueryBuilder('rpf')
            ->select('rpf')
            ->where('rpf.workerid = :worker')
            ->andWhere('rpf.projectid IN (:projects)')
            ->setParameter('projects', $projectIds)
            ->setParameter('worker', $worker)
            ->getQuery()
            ;
        return $qb->getResult();
    }

    public function updateWorkerRoles($worker)
    {
        $str = '[';

        foreach ($worker->getUser()->getRoles() as $role) {
            $str .= $role . ';';
        }

        $str[strlen($str) - 1] = ']';

        $qb = $this->createQueryBuilder('rpf')
            ->update()
            ->where('rpf.workerid = :worker')
            ->set('rpf.roles', ':roles')
            ->setParameter('roles', $str)
            ->setParameter('worker', $worker)
            ->getQuery()
            ->execute()
            ;
    }


}
