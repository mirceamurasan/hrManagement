<?php

namespace FullSix\ProjectForecastBundle\Repository;

use Doctrine\ORM\EntityRepository;

class TasksloadsRepository extends EntityRepository {
    function findNoTrashedByTaskId($id) {
        return $this->getEntityManager()
                        ->createQuery('SELECT tl
                                       FROM FullSix\ProjectForecastBundle\Entity\Tasksloads tl
                                       WHERE tl.tasksidtasks = ?1
                                       AND tl.isTrashed = false
                                       ORDER BY tl.tasksloadsvolume DESC')
                        ->setParameter(1, $id)
                        ->getResult();
    }

    function findTotalVolumeTaskIdGroupByJobs($taskids) {
        return $this->getEntityManager()
            ->createQuery('
                            SELECT      t.id taskid,
                                        j.id jobid,
                                        j.jobslabel,
                                        SUM(tl.tasksloadsvolume) volume
                            FROM        FullSix\ProjectForecastBundle\Entity\Tasksloads tl
                            JOIN        tl.tasksidtasks t
                            JOIN        tl.jobsidjobs j
                            WHERE       t.id IN ('.implode(', ', $taskids).')
                            GROUP BY    t.id,
                                        jobid,
                                        j.jobslabel
                            ORDER BY    taskid,
                                        j.jobslabel
                        ')
            ->getResult();
    }

    function findOutVolumeTaskIdGroupByJobs($taskids) {
        return $this->getEntityManager()
            ->createQuery('
                            SELECT      t.id taskid,
                                        j.id jobid,
                                        j.jobslabel,
                                        SUM(tl.tasksloadsvolume) volume
                            FROM        FullSix\ProjectForecastBundle\Entity\Tasksloads tl
                            JOIN        tl.tasksidtasks t
                            JOIN        tl.jobsidjobs j
                            WHERE       t.id IN ('.implode(', ', $taskids).')
                            AND         tl.providersidproviders IS NOT NULL
                            GROUP BY    t.id,
                                        jobid,
                                        j.jobslabel
                            ORDER BY    taskid,
                                        j.jobslabel
                        ')
            ->getResult();
    }

}
