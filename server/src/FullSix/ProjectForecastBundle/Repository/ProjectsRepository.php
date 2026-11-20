<?php

namespace FullSix\ProjectForecastBundle\Repository;

use Doctrine\ORM\EntityRepository;
use FullSix\ProjectForecastBundle\Entity\Tasks;

class ProjectsRepository extends EntityRepository {

    public function findDatesByProjetcTasks($id)
    {
    $qb = $this->createQueryBuilder('p')
            ->select('p')
            ->leftJoin('p.projectstasks', 't')
            ->select('MAX(t.tasksendat) maxDate, MIN(t.tasksbeginat) minDate')
            ->where('p.isTrashed = false')
            ->andWhere('p.mergedWith IS null')
            ->andWhere('t.isTrashed = false')
            ->andWhere('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleResult()
            ;
        return $qb;
    }

    public function findNoTrashProjects() {
        return $this->getEntityManager()
                        ->createQuery('SELECT p, c
                                       FROM FullSix\ProjectForecastBundle\Entity\Projects p
                                       JOIN p.customersidcustomers c
                                       WHERE p.isTrashed = false
                                       ORDER BY c.customersname, p.projectsname ASC')
                        ->getResult();
    }

    public function findNoTrashProjectsByWorkerAndTimePeriod($workerId, $startDate, $endDate = null)
    {
        if (!$endDate) {
            $endDate = clone $startDate;
            $endDate->modify("+6 days");
        }
        $workerClause = $workerId ? 'AND tl.tasksloadsworkers = :workerId' : '';

        $query = $this->getEntityManager()
                ->createQuery('
                                        SELECT      p
                                        FROM        FullSixProjectForecastBundle:Projects p
                                        JOIN        p.projectstasks t
                                        JOIN        t.taskstasksloads tl
                                        WHERE       tl.isTrashed = 0
                                        ' . $workerClause . '
                                        AND         (
                                                        (t.tasksbeginat <= :startDate AND t.tasksendat >= :startDate) OR
                                                        (t.tasksbeginat >= :startDate AND t.tasksendat <= :endDate) OR
                                                        (t.tasksbeginat <= :endDate AND t.tasksendat >= :endDate)
                                                    )
                                        ORDER BY    p.projectsname ASC,
                                                    p.projectfirsttasktdate ASC,
                                                    p.projectlasttasktdate ASC,
                                                    p.id ASC
                                        ')
                ->setParameter(':startDate', $startDate->format('Y-m-d'))
                ->setParameter(':endDate', $endDate->format('Y-m-d'));

        if ($workerId) {
            $query->setParameter(':workerId', $workerId);
        }
        return $query->getResult();
    }

    public function findProjectsToUpdate($limit = 10, $worker = null) {
        $datelimit = new \DateTime();
        $datelimit->sub(new \DateInterval('P'.$limit.'D'));
        $workerClause = $worker ? 'AND rp.workerid = :worker' : null;

        $query = $this->getEntityManager()
            ->createQuery('
                                        SELECT      p,
                                                    c
                                        FROM        FullSixProjectForecastBundle:Projects p
                                        JOIN        p.customersidcustomers c
                                        JOIN        p.resourceprofilesflat rp
                                        WHERE       p.isTrashed = 0
                                        AND         p.projectsupdated < ?2 '
                                        . $workerClause .
                                        ' ORDER BY  p.projectsupdated DESC,
                                                    p.projectsname ASC,
                                                    p.projectfirsttasktdate ASC,
                                                    p.projectlasttasktdate ASC,
                                                    p.id ASC
                                        ')
            ->setParameter(2, $datelimit->format('Y-m-d'));

        if ($worker) {
            $query->setParameter(':worker', $worker);
        }
        return $query->getResult();
    }

    public function getProjectsByCustomer($customerId) {
        $qb = $this->createQueryBuilder('p')
            ->select('p')
            ->innerJoin('p.resourceprofilesflat', 'rpf')
            ->leftJoin('FullSixProjectForecastBundle:ResourceProfile', 'rp', 'WITH', 'rp.workerid = rpf.workerid')
            ->where('rp.customerid = :customerId')
            ->setParameter('customerId', $customerId)
            ->getQuery()
            ->getResult();
            ;
        return $qb;
    }

    /*Searches for projects that are not assigned to this customer on ResourceProfileFlat entity.*/
    public function getProjectsThatAreNotAssignedToCustomer($customer, $projectIds) {
        $qb = $this->createQueryBuilder('p')
            ->select('p')
            ->innerJoin('p.customersidcustomers', 'c')
            ->leftJoin('p.resourceprofilesflat', 'rpf')
            ->leftJoin('FullSixProjectForecastBundle:ResourceProfile', 'rp', 'WITH', 'rp.workerid = rpf.workerid')
            ->where('p.customersidcustomers = :customer');
        if ($projectIds) {
            $qb = $qb->andWhere('rpf.projectid NOT IN (:projects)')
                ->setParameter('projects', $projectIds);
        }
        $qb = $qb->setParameter('customer', $customer)
            ->getQuery()
            ;
        return $qb->getResult();
    }

    /* Selects the projects that are active and have not been merged before with another project,
     without selecting the current Project.*/
    public function getActiveProjectsForMerge($sourceId)
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.isTrashed = false')
            ->andWhere('p.mergedWith IS null')
            ->andWhere('p.id != :sourceId')
            ->setParameter('sourceId', $sourceId)
            ->getQuery()
            ->getResult()
            ;

        return $qb;
    }

    /**
     * Returns the project with a specific sapNumber
     *
     * @param string $sapNumber
     *
     * @return array
     */
    public function getProjectsBySapNumber($sapNumber) {
        $qb = $this->createQueryBuilder('p')
            ->where('p.sapnumber = :sapNumber')
            ->setParameter('sapNumber', $sapNumber)
            ->getQuery();
        try {
            return $qb->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

}
