<?php
namespace FullSix\ProjectForecastBundle\Repository;

use Doctrine\ORM\EntityRepository;
use FullSix\ProjectForecastBundle\Entity\Tasks;

class TasksRepository extends EntityRepository {

    function findNoTrashedTasksByProjectId($project)
    {
        $qb = $this->createQueryBuilder('t')
            ->select('t')
            ->where('t.projectsidprojects = :project')
            ->andWhere('t.isTrashed = false')
            ->setParameter('project', $project)
            ->getQuery()
            ->getResult()
            ;
        return $qb;
    }

    function findMaxOrdreByProjectId($projectId) {
        $qb = $this->createQueryBuilder('t')
            ->select('MAX (t.tasksorder)')
            ->leftJoin('t.projectsidprojects', 'p')
            ->where('p.id = :projectId')
            ->andWhere('t.isTrashed = false')
            ->setParameter('projectId', $projectId)
            ->getQuery()
            ->getSingleScalarResult()
            ;
        return $qb;
    }

    /**
     * Transmet les Task à partir d'une liste d'ID de projets
     * @param $ids array
     * @return array
     */
    function findByProjectIdsAndWorkerOrderByDateAndName($ids, $workerId = null) {
        $workerClause = $workerId !== NULL ? $workerId > 0
                ? "AND tl.tasksloadsworkers = {$workerId}" : "AND tl.tasksloadsworkers is NULL" : "";

                return $this->getEntityManager()
                        ->createQuery('
                                        SELECT      t
                                        FROM        FullSix\ProjectForecastBundle\Entity\Tasks t
                                        JOIN        t.taskstasksloads tl
                                        WHERE       t.projectsidprojects IN ('.implode(', ', $ids).') '
                                        . $workerClause .
                                        ' ORDER BY  t.tasksbeginat ASC,
                                                    t.tasksendat ASC,
                                                    t.tasksname ASC
                                    ')
                        ->getResult();
    }
}
