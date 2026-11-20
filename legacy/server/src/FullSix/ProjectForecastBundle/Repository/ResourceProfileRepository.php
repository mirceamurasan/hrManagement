<?php

namespace FullSix\ProjectForecastBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ResourceProfileRepository extends EntityRepository
{
    public function updateWorkerRoles($worker)
    {
        $str = '[';

        foreach ($worker->getUser()->getRoles() as $role) {
            $str .= $role . ';';
        }

        $str[strlen($str) - 1] = ']';

        $qb = $this->createQueryBuilder('rp')
            ->update()
            ->where('rp.workerid = :worker')
            ->set('rp.roles', ':roles')
            ->setParameter('roles', $str)
            ->setParameter('worker', $worker)
            ->getQuery()
            ->execute()
            ;
    }
}
