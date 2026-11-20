<?php

namespace FullSix\ProjectForecastBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

class WorkersRepository extends EntityRepository {

    public function getFullname()
    {
        return $this->getFirstname() . ' ' . $this->getLastname();
    }

    public function getByLikeLastname($lastname)
    {
        $lastname = filter_var($lastname, FILTER_SANITIZE_STRING);

        return $this->getEntityManager()
                        ->createQuery('SELECT   w.lastname, w.firstname
                           FROM     FullSix\ProjectForecastBundle\Entity\Workers w
                           WHERE    w.lastname LIKE ?1
                           ORDER BY w.lastname ASC')
                        ->setParameter(1, '%' . $lastname . '%')
                        ->getResult();
    }

    public function getWorkersWithActiveTaskLoads()
    {
        return $this->getEntityManager()
                        ->createQuery('
                            SELECT      w
                            FROM        FullSix\ProjectForecastBundle\Entity\Workers w
                            JOIN        FullSix\ProjectForecastBundle\Entity\Tasksloads tl
                            WITH        tl.tasksloadsworkers = w.id
                            JOIN        w.user u
                            WHERE       tl.isTrashed = 0
                            ORDER BY    u.userslastname ASC
                        ')

                        ->getResult();
    }
}
