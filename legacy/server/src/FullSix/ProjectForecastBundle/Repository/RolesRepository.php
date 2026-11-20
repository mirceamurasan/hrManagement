<?php

namespace FullSix\ProjectForecastBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

class RolesRepository extends EntityRepository
{


    public function getRolesAsArray($onlyUserRoles)
    {
        if ($onlyUserRoles) {
            $qb = $this->createQueryBuilder('r')
                ->where('r.roleForUser = 1')
                ->getQuery()
                ->getResult();
        } else {
            $qb = $this->createQueryBuilder('r')
                ->getQuery()
                ->getResult();
        }

       $rolesArray = array();
        foreach ($qb as $role) {


            $rolesArray[$role->getValue()] = $role->getName();
        }

        return $rolesArray;
    }
}